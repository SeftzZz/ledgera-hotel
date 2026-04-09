<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->options('(:any)', function () {
    return response()
        ->setStatusCode(200);
});

$routes->get('/', 'Home::index');

/* =========================
 * AUTH
 * ========================= */
$routes->get('login', 'Auth\Login::index');
$routes->post('login', 'Auth\Login::auth');
$routes->get('logout', 'Auth\Login::logout');
$routes->get('forgot-password', 'Auth\Login::forgotPassword');
$routes->post('send-reset-link', 'Auth\Login::sendResetLink');
$routes->get('reset-password', 'Auth\Login::resetPassword');
$routes->post('update-password', 'Auth\Login::updatePassword');

$routes->get('test-email', 'TestEmail::send');

$routes->group('admin', ['filter' => 'auth'], function($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    $routes->get('dashboard/calendar', 'Admin\Dashboard::calendar', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('dashboard/calendar-attendance/(:num)', 'Admin\Dashboard::attendanceByJob/$1', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);

    $routes->get('hotels', 'Admin\Hotels::index', ['filter' => 'role:admin']);
    $routes->post('hotels/datatable', 'Admin\Hotels::datatable', ['filter' => 'role:admin']);
    $routes->post('hotels/store', 'Admin\Hotels::store', ['filter' => 'role:admin']);
    $routes->post('hotels/get', 'Admin\Hotels::getById', ['filter' => 'role:admin']);
    $routes->post('hotels/update', 'Admin\Hotels::update', ['filter' => 'role:admin']);
    $routes->post('hotels/delete', 'Admin\Hotels::delete', ['filter' => 'role:admin']);
    $routes->post('hotels/get-total', 'Admin\Hotels::getTotalHotels');

    $routes->get('business', 'Admin\Business::index', ['filter' => 'role:admin']);
    $routes->post('business/datatable', 'Admin\Business::datatable', ['filter' => 'role:admin']);
    $routes->post('business/store', 'Admin\Business::store', ['filter' => 'role:admin']);
    $routes->post('business/get', 'Admin\Business::getById', ['filter' => 'role:admin']);
    $routes->post('business/update', 'Admin\Business::update', ['filter' => 'role:admin']);
    $routes->post('business/delete', 'Admin\Business::delete', ['filter' => 'role:admin']);
    $routes->post('business/get-total', 'Admin\Business::getTotalHotels');

    $routes->get('attendance', 'Admin\Attendance::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('attendance/datatable', 'Admin\Attendance::datatable', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('attendance/detail', 'Admin\Attendance::detail', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('attendance/rate', 'Admin\Attendance::submitRating', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('attendance/extend-request', 'Admin\Attendance::extendRequest', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);

    $routes->get('users', 'Admin\Users::index', ['filter' => 'role:admin']);
    $routes->post('users/datatable', 'Admin\Users::datatable', ['filter' => 'role:admin']);
    $routes->post('users/store', 'Admin\Users::store', ['filter' => 'role:admin']);
    $routes->post('users/get', 'Admin\Users::getById', ['filter' => 'role:admin']);
    $routes->post('users/update', 'Admin\Users::update', ['filter' => 'role:admin']);
    $routes->post('users/delete', 'Admin\Users::delete', ['filter' => 'role:admin']);
    $routes->post('users/get-partner', 'Admin\Users::getPartner', ['filter' => 'role:admin,hotel_hr,hotel_gm']);

    $routes->get('application', 'Admin\Application::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('application/datatable', 'Admin\Application::datatable', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('application/worker/(:num)', 'Admin\Application::workerDetail/$1', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('application/update-status', 'Admin\Application::updateStatus', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('application/get-total-completed', 'Admin\Application::getTotalCompletedJobs');
    
    $routes->get('job-vacancies', 'Admin\JobVacancies::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('job-vacancies/datatable', 'Admin\JobVacancies::datatable', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('job-vacancies/store', 'Admin\JobVacancies::store', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('job-vacancies/get', 'Admin\JobVacancies::get', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('job-vacancies/update', 'Admin\JobVacancies::update', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('job-vacancies/skills', 'Admin\JobVacancies::skills', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('job-vacancies/coorporate', 'Admin\JobVacancies::getCoorporateJobs', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('job-vacancies/get-total-postings', 'Admin\JobVacancies::getTotalJobPostings');

    $routes->get('balance', 'Admin\Balance::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/get', 'Admin\Balance::getBalance', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('balance/topup', 'Admin\Balance::topup', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('balance/debit', 'Admin\Balance::debit', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/history', 'Admin\Balance::history', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('balance/datatable', 'Admin\Balance::datatable', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/monthly-jobs', 'Admin\Balance::monthlyJobStats', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('balance/update-revenue', 'Admin\Balance::updateRevenue', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/today-revenue', 'Admin\Balance::getTodayRevenue', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/daily-report', 'Admin\Balance::dailyReport', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/export-report', 'Admin\Balance::exportReportXlsx', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/skill-ratio', 'Admin\Balance::skillRatio', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/skill-ratio-by-department', 'Admin\Balance::skillRatioByDepartment', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->get('balance/department-detail', 'Admin\Balance::departmentDetail');
    $routes->get('balance/department-detail-data', 'Admin\Balance::departmentDetailData');

    $routes->get('transactions', 'Admin\Transactions::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('transactions/datatable', 'Admin\Transactions::datatable', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']); 
    $routes->get('transactions/last-payroll', 'Admin\Transactions::lastPayroll', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);

    $routes->get('schedules', 'Admin\Schedules::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('schedules/datatable', 'Admin\Schedules::datatable');
    $routes->get('schedules/create', 'Admin\Schedules::create');
    $routes->post('schedules/store', 'Admin\Schedules::store');
    $routes->get('schedules/(:num)', 'Admin\Schedules::show/$1', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('schedules/revision/(:num)', 'Admin\Schedules::requestRevision/$1');
    $routes->post('schedules/get-detail', 'Admin\Schedules::getDetail');
    $routes->post('schedules/assign-shift', 'Admin\Schedules::assignShift');
    $routes->post('schedules/request-revision', 'Admin\Schedules::requestRevision');
    $routes->post('schedules/update-shift', 'Admin\Schedules::updateShift');
    $routes->post('schedules/delete-shift', 'Admin\Schedules::deleteShift');
    $routes->post('schedules/submit-schedule', 'Admin\Schedules::submitSchedule');

    $routes->get('schedule-approvals', 'Admin\ScheduleApprovals::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('schedule-approvals/approve-plan/(:num)', 'Admin\ScheduleApprovals::approvePlan/$1');
    $routes->post('schedule-approvals/reject-plan/(:num)', 'Admin\ScheduleApprovals::rejectPlan/$1');
    $routes->post('schedule-approvals/approve-revision/(:num)', 'Admin\ScheduleApprovals::approveRevision/$1');
    $routes->post('schedule-approvals/reject-revision/(:num)', 'Admin\ScheduleApprovals::rejectRevision/$1');

    $routes->get('invoices', 'Admin\Invoices::index', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('invoices/datatable', 'Admin\Invoices::datatable', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/print/(:num)', 'Admin\Invoices::print/$1', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/printview/(:num)', 'Admin\Invoices::printView/$1', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/send/(:num)', 'Admin\Invoices::sendEmail/$1', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/create/(:num)', 'Admin\Invoices::create/$1', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/create-week/(:num)/(:num)', 'Admin\Invoices::createWeekly/$1/$2', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->get('invoices/view/(:num)', 'Admin\Invoices::view/$1', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('invoices/get', 'Admin\Invoices::getById', ['filter' => 'role:admin']);
    $routes->post('invoices/update', 'Admin\Invoices::update', ['filter' => 'role:admin']);

    $routes->get('payments', 'Admin\Payments::index', ['filter' => 'role:admin']);
    $routes->post('payments/datatable', 'Admin\Payments::datatable');

    $routes->get('trainings', 'Admin\Trainings::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('trainings/datatable', 'Admin\Trainings::datatable');
    $routes->get('trainings/create', 'Admin\Trainings::create');
    $routes->post('trainings/store', 'Admin\Trainings::store');
    $routes->get('trainings/(:num)', 'Admin\Trainings::show/$1', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('trainings/get-detail', 'Admin\Trainings::getDetail');
    $routes->post('trainings/assign-participant', 'Admin\Trainings::assignParticipant');
    $routes->post('trainings/request-revision', 'Admin\Trainings::requestRevision');

    $routes->get('training-approvals', 'Admin\TrainingApprovals::index', ['filter' => 'role:admin,hotel_hr,hotel_fnb_service,hotel_fnb_production,hotel_fo,hotel_hk,hotel_gm,hotel_fna,hotel_eng,hotel_sales']);
    $routes->post('training-approvals/approve-plan/(:num)', 'Admin\TrainingApprovals::approvePlan/$1');
    $routes->post('training-approvals/reject-plan/(:num)', 'Admin\TrainingApprovals::rejectPlan/$1');
    $routes->post('training-approvals/approve-revision/(:num)', 'Admin\TrainingApprovals::approveRevision/$1');
    $routes->post('training-approvals/reject-revision/(:num)', 'Admin\TrainingApprovals::rejectRevision/$1');

    $routes->get('manpowerreq', 'Admin\ManpowerReq::index', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/datatable', 'Admin\ManpowerReq::datatable', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/store', 'Admin\ManpowerReq::store', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/get', 'Admin\ManpowerReq::getById', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/update', 'Admin\ManpowerReq::update', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/delete', 'Admin\ManpowerReq::delete', ['filter' => 'role:except,worker']);
    $routes->post('manpowerreq/submit', 'Admin\ManpowerReq::submit', ['filter' => 'role:except,worker']);

    $routes->get('manpoweracc', 'Admin\Manpoweracc::index', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('manpoweracc/datatable', 'Admin\Manpoweracc::datatable', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('manpoweracc/get', 'Admin\Manpoweracc::getById', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('manpoweracc/acc', 'Admin\Manpoweracc::acc', ['filter' => 'role:admin,hotel_hr,hotel_gm']);

    $routes->get('skills', 'Admin\Skills::index', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('skills/datatable', 'Admin\Skills::datatable', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('skills/store', 'Admin\Skills::store', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('skills/get', 'Admin\Skills::getById', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('skills/update', 'Admin\Skills::update', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
    $routes->post('skills/delete', 'Admin\Skills::delete', ['filter' => 'role:admin,hotel_hr,hotel_gm']);
});

$routes->group('api', function($routes) {
    $routes->post('auth/login', 'Api\AuthController::login');
    $routes->post('auth/google', 'Api\AuthController::google');
    $routes->post('auth/facebook', 'Api\AuthController::facebook');
    $routes->post('auth/register', 'Api\AuthController::register');
    $routes->post('auth/refresh', 'Api\AuthController::refresh');
    $routes->post('worker/attendance/checkout', 'Api\WorkerController::checkout');
});

$routes->group('api', ['filter' => 'jwt'], function($routes) {

    // =========================
    // WORKER PROFILE
    // =========================
    $routes->get('worker/profile', 'Api\WorkerController::profile');
    $routes->put('worker/profile', 'Api\WorkerController::updateProfile');
    $routes->get('worker/me', 'Api\WorkerController::me');

    // =========================
    // WORKER SKILLS
    // =========================
    $routes->get('worker/skills', 'Api\WorkerController::skills');
    $routes->get('worker/my-skills', 'Api\WorkerController::mySkills');
    $routes->post('worker/skills', 'Api\WorkerController::setSkills');

    // =========================
    // WORKER DATA
    // =========================
    $routes->get('worker/jobs', 'Api\WorkerController::jobs');
    $routes->post('worker/experience', 'Api\WorkerController::addExperience');
    $routes->get('worker/experience', 'Api\WorkerController::experiences');
    $routes->post('worker/education', 'Api\WorkerController::addEducation');
    $routes->get('worker/education', 'Api\WorkerController::educations');

    $routes->post('worker/upload/photo', 'Api\WorkerController::uploadPhoto');
    $routes->post('worker/upload/document', 'Api\WorkerController::uploadDocument');
    $routes->get('worker/documents', 'Api\WorkerController::documents');

    // =========================
    // APPLICATION
    // =========================
    $routes->get('worker/application-list', 'Api\WorkerController::applicationList');
    $routes->get('worker/applications', 'Api\WorkerController::applications');
    $routes->get('worker/applications/(:num)', 'Api\WorkerController::applicationDetail/$1');

    // =========================
    // ATTENDANCE 🔥 (FIXED)
    // =========================
    $routes->get('worker/schedule', 'Api\WorkerController::schedule');

    // list attendance (schedule)
    // optional: ?date=YYYY-MM-DD
    $routes->get('worker/attendance', 'Api\WorkerController::attendance');

    // attendance by job
    $routes->get('worker/attendance/job/(:num)', 'Api\WorkerController::attendanceByJob/$1');

    // check-in / check-out
    $routes->post('worker/attendance/checkin', 'Api\WorkerController::checkin');

    // =========================
    // RATING
    // =========================
    $routes->post('worker/rating', 'Api\RatingController::submit');
    $routes->get('worker/ratings', 'Api\RatingController::myRatings');

    // =========================
    // TRAINING
    // =========================
    $routes->get('worker/training-list', 'Api\WorkerController::trainingList');
    $routes->get('worker/trainings', 'Api\WorkerController::trainings');
    $routes->get('worker/training/(:num)', 'Api\WorkerController::trainingDetail/$1');

    // =========================
    // JOB (PUBLIC DATA)
    // =========================
    $routes->get('worker/most-popular', 'Api\WorkerController::mostPopular');
    $routes->get('skills', 'Api\WorkerController::skills');

    $routes->get('jobs', 'Api\JobController::index');
    $routes->get('jobs/(:num)', 'Api\JobController::show/$1');
    $routes->post('jobs/(:num)/apply', 'Api\JobController::apply/$1');
    $routes->post('jobs', 'Api\JobController::create');

    // =========================
    // COMPANY
    // =========================
    $routes->get('company/hotels', 'Api\CompanyController::index');

    // =========================
    // PUSH NOTIFICATION
    // =========================
    $routes->post('worker/push-notification/register', 'Api\WorkerController::pushNotificationRegister');

    // =========================
    // WORKER / BALANCE
    // =========================
    $routes->get('worker/wallet-detail', 'Api\WorkerController::walletDetail');
});