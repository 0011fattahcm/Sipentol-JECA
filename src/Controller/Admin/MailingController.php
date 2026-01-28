<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
use Cake\Mailer\Mailer;
use Cake\Log\Log;

class MailingController extends AppController
{
    // simpan Table instance (tanpa loadModel)
    private $MailingsTable;
    private $UsersTable;
    private $OnlineTestsTable;

    public function initialize(): void
    {
        parent::initialize();

        $this->viewBuilder()->setLayout('admin');

        // CakePHP-friendly: ambil Table dari TableLocator
        $this->MailingsTable    = $this->fetchTable('Mailings');
        $this->UsersTable       = $this->fetchTable('Users');
        $this->OnlineTestsTable = $this->fetchTable('OnlineTests');
    }

    public function index()
    {
        $q = (string)$this->request->getQuery('q', '');
        $limit = (int)$this->request->getQuery('limit', 10);
        if (!in_array($limit, [10, 20, 50, 100], true)) $limit = 10;

        $query = $this->MailingsTable->find()->orderDesc('id');

        if ($q !== '') {
            $query->where([
                'OR' => [
                    'Mailings.subject LIKE' => '%' . $q . '%',
                    'Mailings.body_html LIKE' => '%' . $q . '%',
                    'Mailings.target LIKE' => '%' . $q . '%',
                ]
            ]);
        }

        $mailings = $this->paginate($query, ['limit' => $limit]);
        $this->set(compact('mailings', 'q', 'limit'));
    }

    public function add()
    {
        $users = $this->UsersTable->find()
            ->select(['id', 'nama_lengkap', 'email'])
            ->orderAsc('nama_lengkap')
            ->all();

        if ($this->request->is('post')) {
            $subject = trim((string)$this->request->getData('subject'));

            // add.php kirim key "body" (hidden input), bukan body_html
            $bodyHtml = (string)(
                $this->request->getData('body_html')
                ?? $this->request->getData('body')
                ?? ''
            );

            // add.php pakai name="is_all"
            $targetAll =
                ((string)$this->request->getData('target_all') === '1')
                || ((string)$this->request->getData('is_all') === '1');

            $userIds = (array)$this->request->getData('user_ids');
            $userIds = array_values(array_filter(array_map('intval', $userIds), fn($x) => $x > 0));

            if ($subject === '' || trim(strip_tags($bodyHtml)) === '') {
                $this->Flash->error('Subject dan Body wajib diisi.');
                $this->set(compact('users'));
                return;
            }

            if (!$targetAll && count($userIds) === 0) {
                $this->Flash->error('Pilih minimal 1 penerima, atau centang "semua user".');
                $this->set(compact('users'));
                return;
            }

            // recipients
            $recipientsQ = $this->UsersTable->find()
                ->select(['id', 'nama_lengkap', 'email'])
                ->where(['email IS NOT' => null])
                ->andWhere(['email <>' => '']);

            if (!$targetAll) {
                $recipientsQ->andWhere(['id IN' => $userIds]);
            }

            $recipients = $recipientsQ->all()->toList();

            if (count($recipients) === 0) {
                $this->Flash->error('Tidak ada penerima valid (email kosong).');
                $this->set(compact('users'));
                return;
            }

            // simpan mailing record
            $mailing = $this->MailingsTable->newEmptyEntity();
            $mailing->subject = $subject;
            $mailing->body_html = $bodyHtml;
            $mailing->target = $targetAll ? 'semua' : 'tertentu';
            $mailing->user_ids_json = $targetAll ? $this->safeJson([]) : $this->safeJson($userIds);

            $mailing->sent_success = 0;
            $mailing->sent_failed = 0;
            $mailing->sent_total = count($recipients);
            $mailing->sent_at = FrozenTime::now();

            if (!$this->MailingsTable->save($mailing)) {
                $this->Flash->error('Gagal menyimpan data mailing.');
                $this->set(compact('users'));
                return;
            }

            // kirim email
            $sender = 'sipentol@jecaid.com';
            $success = 0;
            $failed = 0;

            foreach ($recipients as $u) {
                try {
                    $onlineTest = $this->OnlineTestsTable->find()
                        ->where(['user_id' => (int)$u->id])
                        ->orderDesc('id')
                        ->first();

                    $renderedSubject = $this->renderTemplate($subject, $u, $onlineTest);
                    $renderedBody    = $this->renderTemplate($bodyHtml, $u, $onlineTest);

                    $mailer = new Mailer('default');
                    $mailer
                        ->setEmailFormat('html')
                        ->setFrom([$sender => 'Sistem Pendaftaran Online LPK JECA'])
                        ->setTo((string)$u->email)
                        ->setSubject($renderedSubject);

                    $mailer->deliver($renderedBody);
                    $success++;
                } catch (\Throwable $e) {
                    $failed++;
                    // LOG WAJIB biar ketahuan kalau SMTP/transport fail
                    Log::error('[Mailing] send fail to ' . (string)$u->email . ' | ' . $e->getMessage());
                }
            }

            // update counters
            $mailing = $this->MailingsTable->get((int)$mailing->id);
            $mailing->sent_success = $success;
            $mailing->sent_failed  = $failed;
            $mailing->sent_total   = count($recipients);
            $mailing->sent_at      = FrozenTime::now();
            $this->MailingsTable->save($mailing);

            $this->Flash->success("Email diproses. Sukses: {$success}, Gagal: {$failed}, Total: " . count($recipients) . '.');
            return $this->redirect(['action' => 'view', $mailing->id]);
        }

        $this->set(compact('users'));
    }

    public function view($id = null)
    {
        $mailing = $this->MailingsTable->get((int)$id);

        $userIds = [];
        if (!empty($mailing->user_ids_json)) {
            $decoded = json_decode((string)$mailing->user_ids_json, true);
            if (is_array($decoded)) $userIds = array_map('intval', $decoded);
        }

        if ((string)$mailing->target === 'semua') {
            $recipients = $this->UsersTable->find()
                ->select(['id', 'nama_lengkap', 'email'])
                ->where(['email IS NOT' => null])
                ->andWhere(['email <>' => ''])
                ->orderAsc('nama_lengkap')
                ->all()
                ->toList();
        } else {
            $recipients = [];
            if (count($userIds) > 0) {
                $recipients = $this->UsersTable->find()
                    ->select(['id', 'nama_lengkap', 'email'])
                    ->where(['id IN' => $userIds])
                    ->orderAsc('nama_lengkap')
                    ->all()
                    ->toList();
            }
        }

        $this->set(compact('mailing', 'recipients'));
    }

    public function previewUser()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        $userId = (int)($this->request->getData('user_id') ?: $this->request->getQuery('user_id'));
        $subjectTpl = (string)$this->request->getData('subject');

        $bodyTpl = (string)(
            $this->request->getData('body')
            ?? $this->request->getData('body_html')
            ?? ''
        );

        if ($userId <= 0) {
            throw new BadRequestException('user_id invalid');
        }

        $user = $this->UsersTable->find()
            ->select(['id', 'nama_lengkap', 'email'])
            ->where(['id' => $userId])
            ->first();

        if (!$user) {
            throw new BadRequestException('User not found');
        }

        $onlineTest = $this->OnlineTestsTable->find()
            ->where(['user_id' => $userId])
            ->orderDesc('id')
            ->first();

        $renderedSubject = $this->renderTemplate($subjectTpl, $user, $onlineTest);
        $renderedBody    = $this->renderTemplate($bodyTpl, $user, $onlineTest);

        $payload = [
            'ok' => true,
            'subject' => $renderedSubject,
            'body' => $renderedBody,
        ];

        return $this->response
            ->withType('application/json')
            ->withStringBody(json_encode($payload, JSON_UNESCAPED_UNICODE));
    }

    private function renderTemplate(string $tpl, $user, $onlineTest): string
    {
        $map = [
            '{{nama_lengkap}}' => (string)($user->nama_lengkap ?? ''),
            '{{email}}' => (string)($user->email ?? ''),
            '{{test_access_id}}' => $onlineTest ? (string)($onlineTest->test_access_id ?? '') : '',
            '{{test_url}}' => $onlineTest ? (string)($onlineTest->test_url ?? '') : '',
        ];

        return strtr($tpl, $map);
    }

    private function safeJson(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '[]';
    }
}
