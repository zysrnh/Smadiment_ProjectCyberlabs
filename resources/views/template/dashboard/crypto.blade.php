@extends('layouts.app')

@section('title', 'Crypto')

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
                <!-- [ bitcoin-wallet section ] start-->
                <div class="col-md-6 col-xl-4">
                    <div class="card bg-brand-color-1 bitcoin-wallet">
                        <div class="card-body">
                            <h5 class="text-white mb-2">Bitcoin Wallet</h5>
                            <h2 class="text-white mb-2 f-w-300">$9,302</h2>
                            <span class="text-white d-block">Ratings by Market Capitalization</span>
                            <i class="ti ti-currency-bitcoin f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xl-4">
                    <div class="card bg-brand-color-2 bitcoin-wallet">
                        <div class="card-body">
                            <h5 class="text-white mb-2">Bitcoin Wallet</h5>
                            <h2 class="text-white mb-2 f-w-300">$8,101</h2>
                            <span class="text-white d-block">Ratings by Market Capitalization</span>
                            <i class="ti ti-currency-dollar f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xl-4">
                    <div class="card bg-primary bitcoin-wallet">
                        <div class="card-body">
                            <h5 class="text-white mb-2">Bitcoin Wallet</h5>
                            <h2 class="text-white mb-2 f-w-300">$7,501</h2>
                            <span class="text-white d-block">Ratings by Market Capitalization</span>
                            <i class="ti ti-currency-pound f-70 text-white"></i>
                        </div>
                    </div>
                </div>
                <!-- [ bitcoin-wallet section ] end-->

                <!-- [ statistic-line chat ] start -->
                <div class="col-xl-8 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Statistics</h5>
                        </div>
                        <div class="card-body">
                            <div id="line-area2" class="lineAreaDashboard" style="height: 330px"></div>
                        </div>
                    </div>
                </div>
                <!-- [ statistic-line chat ] end -->

                <!-- [ notifications section ] start -->
                <div class="col-xl-4 col-md-12">
                    <div class="card note-bar">
                        <div class="card-header">
                            <h5>Notifications</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="d-flex friendlist-box">
                                <div class="me-3 flex-shrink-0">
                                    <i class="ph ph-bell f-30"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>New order received</h6>
                                    <span class="f-12 float-end text-muted">12.56</span>
                                    <p class="text-muted m-0">2 unread notification</p>
                                </div>
                            </div>
                            <div class="d-flex friendlist-box border-top">
                                <div class="me-3 flex-shrink-0">
                                    <i class="ph ph-bell f-30"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>New user register</h6>
                                    <span class="f-12 float-end text-muted">12.36</span>
                                    <p class="text-muted m-0">xx messages</p>
                                </div>
                            </div>
                            <div class="d-flex friendlist-box border-top">
                                <div class="me-3 flex-shrink-0">
                                    <i class="ph ph-bell f-30"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>New order register</h6>
                                    <span class="f-12 float-end text-muted">11.45</span>
                                    <p class="text-muted m-0">2 read notification</p>
                                </div>
                            </div>
                            <div class="d-flex friendlist-box border-top">
                                <div class="me-3 flex-shrink-0">
                                    <i class="ph ph-bell f-30"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6>New order prepend</h6>
                                    <span class="f-12 float-end text-muted">9.39</span>
                                    <p class="text-muted m-0">xx messages</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- [ notifications section ] end-->

                <!-- [ worldLow section ] start -->
                <div class="col-xl-8 col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5>Users From United States</h5>
                        </div>
                        <div class="card-body">
                            <div id="world-low" style="height: 350px"></div>
                        </div>
                    </div>
                </div>
                <!-- [ worldLow section ] end-->

                <!-- [ statistic chat ] start -->
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-header">
                            <h5>Statistics</h5>
                        </div>
                        <div class="card-body">
                            <h3 class="f-w-300">$894.39</h3>
                        </div>
                        <div id="Earnings-chart"></div>
                    </div>
                </div>
                <!-- [ statistic chat ] end -->
            </div>
            <!-- [ Main Content ] end -->
        </div>
    </div>
@endsection