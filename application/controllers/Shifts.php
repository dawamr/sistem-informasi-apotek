<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Shifts extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) { redirect('auth'); }
        $this->load->database();
        $this->load->model('User_model');
    }

    // Rules listing (hierarchical assignment)
    public function rules()
    {
        // Ensure table exists
        $rules = array();
        if ($this->db->table_exists('shift_rules')) {
            $rules = $this->db->order_by('id','DESC')->get('shift_rules')->result_array();
        }
        // user map for display
        $user_map = array();
        $users = $this->db->select('id,name')->order_by('name','ASC')->get('users')->result_array();
        foreach ($users as $u) { $user_map[(int)$u['id']] = $u['name']; }
        $data = array(
            'page_title' => 'Aturan Penugasan Shift',
            'current_page' => 'shifts',
            'rules' => $rules,
            'user_map' => $user_map,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/rules_list', $data);
        $this->load->view('templates/footer', $data);
    }

    public function rules_create()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/rules'); return; }
        if ($this->input->method() === 'post') {
            $user_id = (int)$this->input->post('user_id');
            $shift_name = $this->input->post('shift_name', TRUE);
            $days = $this->input->post('days'); // array of numbers (1-7)
            $start = $this->input->post('effective_start');
            $end = $this->input->post('effective_end');
            $active = $this->input->post('active') ? 1 : 0;
            $days_str = is_array($days) ? implode(',', array_map('intval', $days)) : '';
            $ok = false;
            if ($this->db->table_exists('shift_rules')) {
                $ok = $this->db->insert('shift_rules', array(
                    'user_id' => $user_id,
                    'shift_name' => $shift_name,
                    'days_of_week' => $days_str,
                    'effective_start' => $start ?: null,
                    'effective_end' => $end ?: null,
                    'active' => $active,
                ));
            }
            $this->session->set_flashdata($ok?'success':'danger', $ok?'Aturan berhasil ditambahkan':'Gagal menambahkan aturan (pastikan tabel shift_rules ada)');
            redirect('shifts/rules');
            return;
        }
        $users = $this->db->order_by('name','ASC')->get('users')->result_array();
        $data = array(
            'page_title' => 'Tambah Aturan Shift',
            'current_page' => 'shifts',
            'users' => $users,
            'rule' => null,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/rules_form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function rules_edit($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/rules'); return; }
        if (!$this->db->table_exists('shift_rules')) { show_404(); }
        $rule = $this->db->where('id', (int)$id)->get('shift_rules')->row_array();
        if (!$rule) { $this->session->set_flashdata('danger','Aturan tidak ditemukan'); redirect('shifts/rules'); }
        if ($this->input->method() === 'post') {
            $user_id = (int)$this->input->post('user_id');
            $shift_name = $this->input->post('shift_name', TRUE);
            $days = $this->input->post('days');
            $start = $this->input->post('effective_start');
            $end = $this->input->post('effective_end');
            $active = $this->input->post('active') ? 1 : 0;
            $days_str = is_array($days) ? implode(',', array_map('intval', $days)) : '';
            $ok = $this->db->where('id', (int)$id)->update('shift_rules', array(
                'user_id' => $user_id,
                'shift_name' => $shift_name,
                'days_of_week' => $days_str,
                'effective_start' => $start ?: null,
                'effective_end' => $end ?: null,
                'active' => $active,
            ));
            if ($ok) {
                // Auto-assign from rules for future dates
                $this->_auto_assign_from_rules($rule['effective_start']);
                $this->session->set_flashdata('success', 'Aturan diperbarui');
            } else {
                $this->session->set_flashdata('danger', 'Gagal memperbarui aturan');
            }
            redirect('shifts/rules');
            return;
        }
        $users = $this->db->order_by('name','ASC')->get('users')->result_array();
        $data = array(
            'page_title' => 'Edit Aturan Shift',
            'current_page' => 'shifts',
            'users' => $users,
            'rule' => $rule,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/rules_form', $data);
        $this->load->view('templates/footer', $data);
    }

    public function rules_delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/rules'); return; }
        if (!$this->db->table_exists('shift_rules')) { show_404(); }
        $ok = $this->db->where('id', (int)$id)->delete('shift_rules');
        $this->session->set_flashdata($ok?'success':'danger', $ok?'Aturan dihapus':'Gagal menghapus aturan');
        redirect('shifts/rules');
    }

    // CSV Export for shift rules (placeholder for future implementation)
    public function rules_export()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/rules'); return; }
        // TODO: Implement CSV export functionality
        // - Query all shift rules with user names
        // - Generate CSV headers: User, Shift Name, Days of Week, Effective Start, Effective End, Active
        // - Output CSV file with proper headers for download
        $this->session->set_flashdata('info', 'Export CSV belum diimplementasikan');
        redirect('shifts/rules');
    }

    // CSV Import for shift rules (placeholder for future implementation)
    public function rules_import()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/rules'); return; }
        // TODO: Implement CSV import functionality
        // - Accept CSV file upload
        // - Validate CSV format and data
        // - Parse and insert/update shift rules
        // - Handle errors and provide feedback
        $this->session->set_flashdata('info', 'Import CSV belum diimplementasikan');
        redirect('shifts/rules');
    }

    

    private function _auto_assign_from_rules($date)
    {
        if (!$this->db->table_exists('shift_rules')) { return; }
        $today = date('Y-m-d');
        $now = date('H:i:s');
        if ($date < $today) { return; }
        // ignore holidays
        if ($this->db->where('date', $date)->count_all_results('holidays') > 0) { return; }
        // shifts on date
        $shifts = $this->db->where('date', $date)->get('shifts')->result_array();
        if (empty($shifts)) { return; }
        $dayN = (int)date('N', strtotime($date));
        $rules = $this->db->where('active', 1)->get('shift_rules')->result_array();
        if (empty($rules)) { return; }
        // index rules by shift_name and check day range
        foreach ($shifts as $s) {
            // skip running/past shift in current day
            if ($date === $today && $s['start_time'] <= $now) { continue; }
            foreach ($rules as $r) {
                if ($r['shift_name'] !== $s['shift_name']) { continue; }
                // check effective range
                if (!empty($r['effective_start']) && $r['effective_start'] > $date) { continue; }
                if (!empty($r['effective_end']) && $r['effective_end'] < $date) { continue; }
                // check day of week
                $days = array_filter(explode(',', (string)$r['days_of_week']));
                if (!empty($days) && !in_array((string)$dayN, $days, true)) { continue; }
                // ensure no duplicate attendance
                $exists = $this->db->where('user_id', (int)$r['user_id'])
                                   ->where('shift_id', (int)$s['id'])
                                   ->where('date', $date)
                                   ->count_all_results('attendances') > 0;
                if (!$exists) {
                    $this->db->insert('attendances', array(
                        'user_id' => (int)$r['user_id'],
                        'shift_id' => (int)$s['id'],
                        'date' => $date,
                        'status' => 'alpha',
                        'checkin_time' => null,
                        'checkout_time' => null,
                        'notes' => null,
                    ));
                }
            }
        }
    }

    // Create single shift
    public function create()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/list'); return; }
        if ($this->input->method() === 'post') {
            $date = $this->input->post('date');
            $name = $this->input->post('shift_name', TRUE);
            $start = $this->input->post('start_time');
            $end = $this->input->post('end_time');
            if (!$date || !$name || !$start || !$end) {
                $this->session->set_flashdata('danger', 'Input tidak lengkap');
                redirect('shifts/create');
                return;
            }
            if ($this->_overlaps($date, $start, $end, null)) {
                $this->session->set_flashdata('danger', 'Jam shift bertabrakan dengan shift lain pada tanggal tersebut');
                redirect('shifts/create');
                return;
            }
            $ok = $this->db->insert('shifts', array(
                'date' => $date,
                'shift_name' => $name,
                'start_time' => $start,
                'end_time' => $end,
            ));
            if ($ok) {
                // Auto-assign from rules for future dates
                $this->_auto_assign_from_rules($date);
                $this->session->set_flashdata('success', 'Shift berhasil ditambahkan');
            } else {
                $this->session->set_flashdata('danger', 'Gagal menambahkan shift');
            }
            redirect('shifts/list');
            return;
        }
        $data = array(
            'page_title' => 'Tambah Shift',
            'current_page' => 'shifts',
            'shift' => null,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/form', $data);
        $this->load->view('templates/footer', $data);
    }

    // Edit single shift
    public function edit($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/list'); return; }
        $row = $this->db->where('id', (int)$id)->get('shifts')->row_array();
        if (!$row) { $this->session->set_flashdata('danger', 'Shift tidak ditemukan'); redirect('shifts/list'); }
        if ($this->input->method() === 'post') {
            $update = array(
                'date' => $this->input->post('date'),
                'shift_name' => $this->input->post('shift_name', TRUE),
                'start_time' => $this->input->post('start_time'),
                'end_time' => $this->input->post('end_time'),
            );
            if (!$update['date'] || !$update['shift_name'] || !$update['start_time'] || !$update['end_time']) {
                $this->session->set_flashdata('danger', 'Input tidak lengkap');
                redirect('shifts/edit/'.$id);
                return;
            }
            if ($this->_overlaps($update['date'], $update['start_time'], $update['end_time'], (int)$id)) {
                $this->session->set_flashdata('danger', 'Jam shift bertabrakan dengan shift lain pada tanggal tersebut');
                redirect('shifts/edit/'.$id);
                return;
            }
            $ok = $this->db->where('id', (int)$id)->update('shifts', $update);
            if ($ok) {
                // Auto-assign from rules for future dates
                $this->_auto_assign_from_rules($update['date']);
                $this->session->set_flashdata('success', 'Shift diperbarui');
            } else {
                $this->session->set_flashdata('danger', 'Gagal memperbarui shift');
            }
            redirect('shifts/list');
            return;
        }
        $data = array(
            'page_title' => 'Edit Shift',
            'current_page' => 'shifts',
            'shift' => $row,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/form', $data);
        $this->load->view('templates/footer', $data);
    }

    // Delete shift
    public function delete($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/list'); return; }
        $this->db->where('id', (int)$id)->delete('shifts');
        $this->session->set_flashdata($this->db->affected_rows() > 0 ? 'success' : 'danger', $this->db->affected_rows() > 0 ? 'Shift dihapus' : 'Gagal menghapus shift');
        redirect('shifts/list');
    }

    // Assign users to a shift (creates attendance rows for that date/shift)
    public function assign($id)
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts/list'); return; }
        $shift = $this->db->where('id', (int)$id)->get('shifts')->row_array();
        if (!$shift) { $this->session->set_flashdata('danger', 'Shift tidak ditemukan'); redirect('shifts/list'); }

        if ($this->input->method() === 'post') {
            $user_ids = $this->input->post('user_ids'); // array
            if (!is_array($user_ids)) { $user_ids = array(); }

            // remove previous assignments for that shift/date to avoid duplicates
            $this->db->where('shift_id', (int)$id)->where('date', $shift['date'])->delete('attendances');

            if (!empty($user_ids)) {
                $batch = array();
                foreach ($user_ids as $uid) {
                    $batch[] = array(
                        'user_id' => (int)$uid,
                        'shift_id' => (int)$id,
                        'date' => $shift['date'],
                        'status' => 'hadir', // scheduled to attend; checkin_time null initially
                        'checkin_time' => null,
                        'checkout_time' => null,
                    );
                }
                if (!empty($batch)) { $this->db->insert_batch('attendances', $batch); }
            }
            $this->session->set_flashdata('success', 'Penugasan shift tersimpan');
            redirect('shifts/list?start='.$shift['date'].'&end='.$shift['date']);
            return;
        }
        // Filters
        $role = $this->input->get('role'); // 'admin'|'apoteker'|''
        $active = $this->input->get('active'); // '1'|'0'|''
        $users = method_exists($this->User_model, 'get_all') ? $this->User_model->get_all() : $this->User_model->get_all_active();
        // apply filters in-memory
        $users = array_values(array_filter($users, function($u) use ($role, $active){
            if ($role !== null && $role !== '' && $u['role'] !== $role) return false;
            if ($active !== null && $active !== '' && (string)$u['active'] !== (string)$active) return false;
            return true;
        }));
        // load existing assignments
        $assigned = $this->db->select('user_id')->from('attendances')->where('shift_id',(int)$id)->where('date',$shift['date'])->get()->result_array();
        $assigned_ids = array_map(function($r){ return (int)$r['user_id']; }, $assigned);
        $data = array(
            'page_title' => 'Assign Pengguna ke Shift',
            'current_page' => 'shifts',
            'shift' => $shift,
            'users' => $users,
            'assigned_ids' => $assigned_ids,
            'filter_role' => $role,
            'filter_active' => $active,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/assign', $data);
        $this->load->view('templates/footer', $data);
    }
    // Calendar view (FullCalendar)
    public function index()
    {
        $month = $this->input->get('month') ?: date('Y-m');
        $data = array(
            'page_title' => 'Jadwal Shift (Kalender)',
            'current_page' => 'shifts',
            'month' => $month,
            'page_scripts' => array('pages/shifts-calendar.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/calendar', $data);
        $this->load->view('templates/footer', $data);
    }

    // List view (DataTables)
    public function listing()
    {
        $start = $this->input->get('start') ?: date('Y-m-01');
        $end = $this->input->get('end') ?: date('Y-m-t');
        $rows = $this->db->where('date >=', $start)
                         ->where('date <=', $end)
                         ->order_by('date', 'ASC')
                         ->get('shifts')->result_array();
        // holidays map
        $holidays = $this->db->where('date >=', $start)->where('date <=', $end)->get('holidays')->result_array();
        $holiday_map = array();
        foreach ($holidays as $h) { $holiday_map[$h['date']] = $h['title']; }
        $data = array(
            'page_title' => 'Jadwal Shift (Daftar)',
            'current_page' => 'shifts',
            'start' => $start,
            'end' => $end,
            'rows' => $rows,
            'holiday_map' => $holiday_map,
            'page_scripts' => array('pages/shifts-list.js'),
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/list', $data);
        $this->load->view('templates/footer', $data);
    }

    // Events feed for FullCalendar
    public function events()
    {
        $start = $this->input->get('start');
        $end = $this->input->get('end');
        if (!$start || !$end) { $start = date('Y-m-01'); $end = date('Y-m-t'); }
        $rows = $this->db->where('date >=', $start)
                         ->where('date <=', $end)
                         ->order_by('date', 'ASC')
                         ->get('shifts')->result_array();
        $events = array();
        foreach ($rows as $r) {
            $title = $r['shift_name'].' ('.$r['start_time'].'-'.$r['end_time'].')';
            $events[] = array(
                'title' => $title,
                'start' => $r['date'].'T'.$r['start_time'],
                'end' => $r['date'].'T'.$r['end_time'],
            );
        }
        // add holidays as background all-day events
        $holidays = $this->db->where('date >=', $start)->where('date <=', $end)->get('holidays')->result_array();
        foreach ($holidays as $h) {
            $events[] = array(
                'title' => $h['title'],
                'start' => $h['date'],
                'end' => $h['date'],
                'allDay' => true,
                'display' => 'background',
                'color' => '#fde68a'
            );
        }
        $this->output->set_content_type('application/json')->set_output(json_encode($events));
    }

    // Weekly setup (simple scheduler for next 7 days)
    public function setup()
    {
        if ($this->session->userdata('role') !== 'admin') { $this->session->set_flashdata('danger','Tidak diizinkan'); redirect('shifts'); return; }
        if ($this->input->method() === 'post') {
            $start_date = $this->input->post('start_date');
            $days = $this->input->post('days'); // array of day configs
            if (!$start_date || empty($days) || !is_array($days)) {
                $this->session->set_flashdata('danger', 'Input tidak valid');
                redirect('shifts/setup');
                return;
            }

            $today = date('Y-m-d');
            $nowTime = date('H:i:s');
            $skipped_past = 0; $upserts = 0; $protected = 0;
            $this->db->trans_start();
            foreach ($days as $d) {
                $date = $d['date'];
                if ($date < $today) { $skipped_past++; continue; }

                $isDayOff = !empty($d['day_off']['enabled']);
                $dayOffTitle = isset($d['day_off']['title']) && trim($d['day_off']['title']) !== '' ? trim($d['day_off']['title']) : 'Libur';

                if ($isDayOff) {
                    // Upsert holiday for the date and remove any shifts on that day
                    $h = $this->db->where('date', $date)->get('holidays')->row_array();
                    if ($h) {
                        $this->db->where('id', (int)$h['id'])->update('holidays', array('title' => $dayOffTitle));
                    } else {
                        $this->db->insert('holidays', array('date' => $date, 'title' => $dayOffTitle));
                    }
                    // Remove only future shifts; protect running/past in current day
                    $shiftRows = $this->db->select('id, start_time')->from('shifts')->where('date', $date)->get()->result_array();
                    foreach ($shiftRows as $sr) {
                        $canDelete = ($date > $today) || ($date === $today && $sr['start_time'] > $nowTime);
                        if ($canDelete) {
                            $this->db->where('shift_id', (int)$sr['id'])->where('date', $date)->delete('attendances');
                            $this->db->where('id', (int)$sr['id'])->delete('shifts');
                            $upserts++;
                        } else {
                            $protected++;
                        }
                    }
                    continue;
                } else {
                    // Ensure no holiday remains for this date
                    $this->db->where('date', $date)->delete('holidays');
                }

                // Morning upsert
                if (!empty($d['morning']['enabled'])) {
                    $mStart = $d['morning']['start'] ?: '08:00';
                    $mEnd   = $d['morning']['end']   ?: '16:00';
                    $exists = $this->db->where('date', $date)->where('shift_name','Pagi')->get('shifts')->row_array();
                    if ($exists) {
                        $this->db->where('id', (int)$exists['id'])->update('shifts', array('start_time'=>$mStart, 'end_time'=>$mEnd));
                        $upserts++;
                    } else {
                        $this->db->insert('shifts', array('date'=>$date, 'shift_name'=>'Pagi', 'start_time'=>$mStart, 'end_time'=>$mEnd));
                        $upserts++;
                    }
                } else {
                    // toggle off: remove existing morning shift if future
                    $exists = $this->db->where('date', $date)->where('shift_name','Pagi')->get('shifts')->row_array();
                    if ($exists) {
                        $canDelete = ($date > $today) || ($date === $today && $exists['start_time'] > $nowTime);
                        if ($canDelete) {
                            $this->db->where('shift_id', (int)$exists['id'])->where('date', $date)->delete('attendances');
                            $this->db->where('id', (int)$exists['id'])->delete('shifts');
                            $upserts++;
                        } else {
                            $protected++;
                        }
                    }
                }

                // Evening upsert
                if (!empty($d['evening']['enabled'])) {
                    $eStart = $d['evening']['start'] ?: '16:00';
                    $eEnd   = $d['evening']['end']   ?: '22:00';
                    $exists = $this->db->where('date', $date)->where('shift_name','Malam')->get('shifts')->row_array();
                    if ($exists) {
                        $this->db->where('id', (int)$exists['id'])->update('shifts', array('start_time'=>$eStart, 'end_time'=>$eEnd));
                        $upserts++;
                    } else {
                        $this->db->insert('shifts', array('date'=>$date, 'shift_name'=>'Malam', 'start_time'=>$eStart, 'end_time'=>$eEnd));
                        $upserts++;
                    }
                } else {
                    // toggle off: remove existing evening shift if future
                    $exists = $this->db->where('date', $date)->where('shift_name','Malam')->get('shifts')->row_array();
                    if ($exists) {
                        $canDelete = ($date > $today) || ($date === $today && $exists['start_time'] > $nowTime);
                        if ($canDelete) {
                            $this->db->where('shift_id', (int)$exists['id'])->where('date', $date)->delete('attendances');
                            $this->db->where('id', (int)$exists['id'])->delete('shifts');
                            $upserts++;
                        } else {
                            $protected++;
                        }
                    }
                }
                // auto-assign from rules for this date (future only)
                $this->_auto_assign_from_rules($date);
            }
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $this->session->set_flashdata('danger', 'Gagal menyimpan jadwal mingguan');
                redirect('shifts/setup');
                return;
            }

            $msg = 'Jadwal mingguan tersimpan. Perubahan: '.$upserts.' entri';
            if ($skipped_past > 0) { $msg .= '. Melewati '.$skipped_past.' hari yang sudah lewat'; }
            $this->session->set_flashdata('success', $msg);
            $end = date('Y-m-d', strtotime($start_date.' +6 days'));
            redirect('shifts/list?start='.$start_date.'&end='.$end);
            return;
        }

        $start = $this->input->get('start_date') ?: date('Y-m-d');
        // load defaults from config
        $this->load->config('shifts');
        $cfg = $this->config->item('shifts');
        $defaults = array(
            'morning_start' => isset($cfg['default_morning_start']) ? $cfg['default_morning_start'] : '08:00',
            'morning_end' => isset($cfg['default_morning_end']) ? $cfg['default_morning_end'] : '16:00',
            'evening_start' => isset($cfg['default_evening_start']) ? $cfg['default_evening_start'] : '16:00',
            'evening_end' => isset($cfg['default_evening_end']) ? $cfg['default_evening_end'] : '22:00',
        );
        // Prefill from DB for selected week (including holidays)
        $days = array();
        $endWeek = date('Y-m-d', strtotime($start.' +6 day'));
        $existing = $this->db->where('date >=', $start)
                              ->where('date <=', $endWeek)
                              ->get('shifts')->result_array();
        $holidays = $this->db->where('date >=', $start)->where('date <=', $endWeek)->get('holidays')->result_array();
        $byDate = array();
        foreach ($existing as $r) {
            $date = $r['date'];
            if (!isset($byDate[$date])) { $byDate[$date] = array(); }
            $byDate[$date][$r['shift_name']] = array(
                'start_time' => substr($r['start_time'], 0, 5),
                'end_time'   => substr($r['end_time'], 0, 5),
            );
        }
        $holiday_map = array(); foreach ($holidays as $h) { $holiday_map[$h['date']] = $h['title']; }
        for ($i=0; $i<7; $i++) {
            $d = date('Y-m-d', strtotime($start." +$i day"));
            $rec = isset($byDate[$d]) ? $byDate[$d] : array();
            $days[] = array(
                'date' => $d,
                'label' => $this->_indo_day_name(date('N', strtotime($d))).' ('.$d.')',
                'morning_enabled' => isset($rec['Pagi']),
                'morning_start' => isset($rec['Pagi']) ? $rec['Pagi']['start_time'] : $defaults['morning_start'],
                'morning_end'   => isset($rec['Pagi']) ? $rec['Pagi']['end_time']   : $defaults['morning_end'],
                'evening_enabled' => isset($rec['Malam']),
                'evening_start' => isset($rec['Malam']) ? $rec['Malam']['start_time'] : $defaults['evening_start'],
                'evening_end'   => isset($rec['Malam']) ? $rec['Malam']['end_time']   : $defaults['evening_end'],
                'day_off_enabled' => isset($holiday_map[$d]),
                'day_off_title'   => isset($holiday_map[$d]) ? $holiday_map[$d] : 'Libur',
            );
        }
        $data = array(
            'page_title' => 'Atur Jadwal Mingguan',
            'current_page' => 'shifts',
            'start_date' => $start,
            'days' => $days,
            'defaults' => $defaults,
        );
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('shifts/setup', $data);
        $this->load->view('templates/footer', $data);
    }

    private function _overlaps($date, $start, $end, $exclude_id = null)
    {
        // Normalize seconds if missing
        $start = strlen($start) === 5 ? ($start.':00') : $start;
        $end = strlen($end) === 5 ? ($end.':00') : $end;
        $this->db->where('date', $date);
        $this->db->where('start_time <', $end);
        $this->db->where('end_time >', $start);
        if (!empty($exclude_id)) { $this->db->where('id <>', (int)$exclude_id); }
        $row = $this->db->get('shifts')->row_array();
        return !empty($row);
    }
    private function _indo_day_name($n)
    {
        $map = array(1=>'Senin',2=>'Selasa',3=>'Rabu',4=>'Kamis',5=>'Jumat',6=>'Sabtu',7=>'Minggu');
        return isset($map[$n]) ? $map[$n] : '';
    }
}
