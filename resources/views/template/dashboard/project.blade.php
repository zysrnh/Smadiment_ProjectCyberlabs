@extends('layouts.app')

@section('title', 'Project')

@section('main')
    <div class="pc-container">
        <div class="pc-content">
            <!-- [ breadcrumb ] start -->
            <div class="page-header">
                <div class="page-block">
                    <div class="row align-items-center">
                        <div class="col-md-12">
                            <div class="page-header-title">
                                <h5 class="mb-0">Dashboard-active</h5>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <ul class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="/dashboard/index.html">Home</a></li>
                                <li class="breadcrumb-item"><a href="javascript: void(0)">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page">Dashboard-active</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- [ breadcrumb ] end -->

            <!-- [ Main Content ] start -->
            <div class="row">
                <!-- [ project-task section ] start -->
                <div class="col-md-6 col-xl-4">
                    <div class="card project-task">
                        <div class="card-body">
                            <div class="row align-items-center justify-content-center">
                                <div class="col">
                                    <h5 class="m-0"><i class="ph ph-note-pencil f-20 align-middle m-r-10"></i>Project
                                        Task</h5>
                                </div>
                                <div class="col-auto">
                                    <label class="badge bg-brand-color-1 text-white f-14 f-w-400 float-end">23%
                                        Done</label>
                                </div>
                            </div>
                            <h6 class="text-muted mt-4 mb-3">Complete Task : 6/10</h6>
                            <div class="progress">
                                <div class="progress-bar" role="progressbar" style="width: 60%; height: 6px"
                                    aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <h6 class="mt-3 mb-0 text-center text-muted">Project Team : 28 Persons</h6>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="mb-4">Sales Statistics</h5>
                            <h3 class="mb-4">2,30,598</h3>
                            <span class="text-muted d-block">Top selling items statistic by last month</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4">
                    <div class="card card-event">
                        <div class="card-body">
                            <div class="row align-items-center justify-content-center">
                                <div class="col">
                                    <h5 class="m-0">Upcoming Event</h5>
                                </div>
                                <div class="col-auto">
                                    <label class="badge bg-brand-color-2 text-white f-14 f-w-400 float-end">34%</label>
                                </div>
                            </div>
                            <h2 class="mt-2">45<sub class="text-muted f-14">Competitors</sub></h2>
                            <h6 class="text-muted mt-3 mb-0">You can participate in event </h6>
                            <i class="ti ti-calendar-check text-info f-50"></i>
                        </div>
                    </div>
                </div>
                <!-- [ project-task section ] end -->

                <!-- [ project-average section ] start -->
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Reply</h5>
                        </div>
                        <div class="card-body">
                            <div class="reply-price">
                                <h3 class="">2.43 h</h3>
                                <span class="text-uppercase">average time for first reply</span>
                            </div>
                            <div id="bar-chart1" class="ChartShadow BarChart barChart1" style="height: 270px"></div>
                        </div>
                    </div>
                </div>
                <!-- [ project-average section ] end -->

                <!-- [ statistic chart ] start -->
                <div class="col-xl-4 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div id="chart-percent" class="chart-percent" style="height: 245px"></div>
                            <div class="row mb-3">
                                <div class="col">
                                    <span class="mb-1 text-muted d-block">23%</span>
                                    <div class="progress" style="height: 5px">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: 23%"
                                            aria-valuenow="23" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <span class="mb-1 text-muted d-block">14%</span>
                                    <div class="progress" style="height: 5px">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: 14%"
                                            aria-valuenow="14" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col">
                                    <span class="mb-1 text-muted d-block">35%</span>
                                    <div class="progress" style="height: 5px">
                                        <div class="progress-bar bg-purple-500" role="progressbar" style="width: 35%"
                                            aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="col">
                                    <span class="mb-1 text-muted d-block">28%</span>
                                    <div class="progress" style="height: 5px">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 28%"
                                            aria-valuenow="28" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ statistic chart ] end -->

                <!-- [ user web-list ] start -->
                <div class="col-xl-4 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-pills" id="myTab1" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="user-tab" data-bs-toggle="tab" href="#user"
                                        role="tab">Today</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="designer-tab" data-bs-toggle="tab" href="#designer"
                                        role="tab">This Week</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="Developer-tab" data-bs-toggle="tab" href="#Developer"
                                        role="tab">All</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content User-Lists" id="myTabContent1">
                                <div class="tab-pane fade show active" id="user" role="tabpanel">
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3784</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Julie Vad</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3544</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>2739</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Frida Thomse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>1032</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>8750</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>3750</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="designer" role="tabpanel">
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3784</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Julie Vad</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3544</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>2739</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Frida Thomse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>1032</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>8750</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>5850</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="Developer" role="tabpanel">
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3784</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Julie Vad</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>3544</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>2739</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Frida Thomse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-down-filled f-22 m-r-10 text-danger"></i>1032</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center m-b-20">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Silje Larsen</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>8750</span>
                                        </div>
                                    </div>
                                    <div class="d-flex friendlist-box align-items-center justify-content-center">
                                        <div class="flex-shrink-0">
                                            <a href="#!"><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></a>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="m-0 d-inline">Storm Hanse</h6>
                                            <span class="float-end d-flex align-items-center"><i
                                                    class="ti ti-caret-up-filled f-22 m-r-10 text-success"></i>5875</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ user web-list ] end -->

                <!-- [ overdue-task section ] start -->
                <div class="col-md-12 col-xl-4">
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h5 class="m-0">Overdue Tasks</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h2 class="m-0">34</h2>
                                    <span class="text-muted">Last Week 60%</span>
                                </div>
                                <div class="col-4 text-end">
                                    <h5 class="text-danger f-w-400">10%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h5 class="m-0">Tasks to Do</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h2 class="m-0">25</h2>
                                    <span class="text-muted">Last Week 40%</span>
                                </div>
                                <div class="col-4 text-end">
                                    <h5 class="text-success f-w-400">30%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-body border-bottom">
                            <h5 class="m-0">Completed Task</h5>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-8">
                                    <h2 class="m-0">19</h2>
                                    <span class="text-muted">Last Week 70%</span>
                                </div>
                                <div class="col-4 text-end">
                                    <h5 class="text-danger f-w-400">25%</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ overdue-task section ] end -->

                <!-- [ user Project list ] start -->
                <div class="col-md-12">
                    <div class="card user-list table-card">
                        <div class="card-header">
                            <h5>User Project List</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>User</th>
                                            <th>project</th>
                                            <th>Completed</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></td>
                                            <td>
                                                <h6 class="mb-1">Social Media App</h6>
                                                <p class="m-0">Assigned to<span class="text-success"> Tristan
                                                        Madsen</span></p>
                                            </td>
                                            <td><span class="pie_1">326,134</span></td>
                                            <td>
                                                <h6 class="m-0">68%</h6>
                                            </td>
                                            <td>
                                                <h6 class="m-0">October 26, 2025</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-2.svg" alt="activity-user" /></td>
                                            <td>
                                                <h6 class="mb-1">Newspaper Wordpress Web</h6>
                                                <p class="m-0">Assigned to<span class="text-success"> Marcus
                                                        Poulsen</span></p>
                                            </td>
                                            <td><span class="pie_2">110,134</span></td>
                                            <td>
                                                <h6 class="m-0">46%</h6>
                                            </td>
                                            <td>
                                                <h6 class="m-0">September 4, 2025</h6>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-3.svg" alt="activity-user" /></td>
                                            <td>
                                                <h6 class="mb-1">Dashboard UI Kit Design</h6>
                                                <p class="m-0">Assigned to<span class="text-success"> Felix
                                                        Johansen</span></p>
                                            </td>
                                            <td><span class="pie_3">226,134</span></td>
                                            <td>
                                                <h6 class="m-0">31%</h6>
                                            </td>
                                            <td>
                                                <h6 class="m-0">November 14, 2025</h6>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><img class="rounded-circle" style="width: 40px"
                                                    src="../assets/images/user/avatar-1.svg" alt="activity-user" /></td>
                                            <td>
                                                <h6 class="mb-1">Social Media App</h6>
                                                <p class="m-0">Assigned to<span class="text-success"> Tristan
                                                        Madsen</span></p>
                                            </td>
                                            <td><span class="pie_4">500,134</span></td>
                                            <td>
                                                <h6 class="m-0">85%</h6>
                                            </td>
                                            <td>
                                                <h6 class="m-0">December 14, 2025</h6>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ user Project list ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection