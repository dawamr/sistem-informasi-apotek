<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH.'core/API_Controller.php');

/**
 * Attendance API Controller
 * 
 * Endpoints:
 * - GET /api/v1/attendance/shift-today - Staff on duty today
 * - GET /api/v1/attendance/summary - Attendance summary
 */
class Attendance extends API_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Attendance_model');
        $this->load->model('Shift_model');
        $this->load->helper('api');
    }

    /**
     * GET /api/v1/attendance/shift-today
     * 
     * Get staff on duty today (or specific date)
     * 
     * Query Parameters:
     * - date (optional): YYYY-MM-DD format, default: today
     */
    public function shift_today()
    {
        try {
            // Get date parameter
            $date = $this->get_date_param('date');

            // Get shifts for the date
            $shifts = $this->Shift_model->get_by_date($date);

            if (empty($shifts)) {
                $this->success_response(array('date' => $date, 'shifts' => array()), 'No shifts scheduled for this date');
                return;
            }

            // Get attendance for each shift
            $shift_data = array();
            foreach ($shifts as $shift) {
                $guards = $this->Attendance_model->get_by_shift($shift['id'], $date);

                // Format guards data
                $formatted_guards = array();
                foreach ($guards as $guard) {
                    $formatted_guards[] = array(
                        'user_id' => (int)$guard['user_id'],
                        'name' => $guard['user_name'],
                        'status' => $guard['status']
                    );
                }

                $shift_data[] = array(
                    'shift_id' => (int)$shift['id'],
                    'shift_name' => $shift['shift_name'],
                    'start_time' => $shift['start_time'],
                    'end_time' => $shift['end_time'],
                    'guards' => $formatted_guards
                );
            }

            // Format response
            $response = array(
                'date' => $date,
                'shifts' => $shift_data
            );

            // Return success response
            $this->success_response($response, 'Staff on duty retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }

    /**
     * GET /api/v1/attendance/summary
     * 
     * Get attendance summary for a date
     * 
     * Query Parameters:
     * - date (required): YYYY-MM-DD format
     */
    public function summary()
    {
        try {
            // Get date parameter
            $date = $this->input->get('date');

            if (empty($date)) {
                $this->error_response('VALIDATION_ERROR', "Parameter 'date' wajib diisi dengan format YYYY-MM-DD", 400);
            }

            if (!$this->validate_date_format($date)) {
                $this->error_response('VALIDATION_ERROR', "Parameter 'date' harus format YYYY-MM-DD", 400);
            }

            // Get attendance summary
            $summary = $this->Attendance_model->get_summary_by_date($date);

            // Get detailed attendance
            $attendances = $this->Attendance_model->get_by_date($date);

            // Format detail
            $detail = array();
            foreach ($attendances as $attendance) {
                $detail[] = array(
                    'user_id' => (int)$attendance['user_id'],
                    'name' => $attendance['user_name'],
                    'shift_name' => $attendance['shift_name'],
                    'status' => $attendance['status']
                );
            }

            // Format response
            $response = array(
                'date' => $date,
                'summary' => array(
                    'total_scheduled' => (int)$summary['total_scheduled'],
                    'present' => (int)$summary['present'],
                    'permission' => (int)$summary['permission'],
                    'sick' => (int)$summary['sick'],
                    'absent' => (int)$summary['absent'],
                    'detail' => $detail
                )
            );

            // Return success response
            $this->success_response($response, 'Attendance summary retrieved successfully');

        } catch (Exception $e) {
            $this->error_response('SERVER_ERROR', $e->getMessage(), 500);
        }
    }
}
