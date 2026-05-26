<?php
// index.php - Main ERP + POS SPA Interface
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$current_user_name = $_SESSION['name'];
$current_user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>VX ERP + POS</title>
    <!-- Web App Meta Tags (PWA Ready) -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#000000">
    <link rel="manifest" href="manifest.json">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS Play CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        dark: {
                            950: '#030303',
                            900: '#0a0a0a',
                            800: '#121212',
                            700: '#1c1c1e',
                            600: '#2c2c2e',
                            500: '#3a3a3c',
                            400: '#48484a',
                        },
                        lime: {
                            DEFAULT: '#ccff00',
                            glow: 'rgba(204, 255, 0, 0.15)',
                            border: 'rgba(204, 255, 0, 0.4)',
                        }
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Safe-area insets for modern mobile screens */
        body {
            padding-bottom: calc(4.5rem + env(safe-area-inset-bottom));
            padding-top: env(safe-area-inset-top);
        }
        .bottom-nav {
            padding-bottom: env(safe-area-inset-bottom);
        }
        
        /* Hide scrollbars but keep functionality */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        
        /* Neon UI Highlights */
        .neon-shadow {
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.15);
        }
        .neon-text-glow {
            text-shadow: 0 0 8px rgba(204, 255, 0, 0.6);
        }
        .neon-border-active {
            border-color: #ccff00 !important;
            box-shadow: 0 0 10px rgba(204, 255, 0, 0.2);
        }
        
        /* Smooth scale transitions */
        .tap-scale {
            transition: transform 0.1s ease;
        }
        .tap-scale:active {
            transform: scale(0.96);
        }

        /* Slide drawer transition */
        .drawer-transition {
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
    </style>
</head>
<body class="bg-dark-950 font-sans text-gray-200 selection:bg-lime selection:text-black min-h-screen pb-20">

    <!-- TOP NAV BAR -->
    <header class="sticky top-0 z-40 bg-dark-950/80 backdrop-blur-xl border-b border-white/5 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <h1 class="text-3xl font-extrabold tracking-tighter text-white">V<span class="text-lime">X</span></h1>
            <div class="h-4 w-px bg-white/20"></div>
            <span class="text-xs font-semibold text-lime tracking-wide bg-lime/10 px-2.5 py-0.5 rounded-full uppercase">Partnership</span>
        </div>
        
        <!-- User cash balance quick widget -->
        <div class="flex items-center gap-3">
            <div class="text-right hidden xs:block">
                <p class="text-[10px] text-gray-400 font-semibold tracking-wider uppercase">Your Cash In Hand</p>
                <p class="text-sm font-bold text-lime" id="topUserCashBalance">Rs. 0.00</p>
            </div>
            
            <!-- Quick action log out -->
            <a href="logout.php" class="bg-dark-800 border border-white/10 hover:border-red-500/50 hover:bg-red-950/30 p-2.5 rounded-xl text-gray-400 hover:text-red-400 transition-all tap-scale" title="Log Out">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
            </a>
        </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <main class="max-w-md mx-auto p-4 space-y-6">

        <!-- ==================== DASHBOARD TAB ==================== -->
        <section id="tab-dashboard" class="space-y-5">
            <!-- Welcome Widget -->
            <div class="flex items-center justify-between bg-dark-900/60 border border-white/10 p-4 rounded-3xl backdrop-blur-xl relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-lime/10 rounded-full blur-2xl pointer-events-none"></div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold">Hello partner,</p>
                    <h3 class="text-lg font-bold text-white"><?= htmlspecialchars($current_user_name) ?></h3>
                </div>
                <span class="text-xs font-bold bg-dark-800 text-gray-300 border border-white/5 py-1.5 px-3 rounded-xl flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Live Session
                </span>
            </div>

            <!-- Main KPIs Grid -->
            <div class="grid grid-cols-2 gap-3.5">
                <!-- Today Sales -->
                <div class="bg-dark-900/60 border border-white/10 p-4 rounded-3xl relative overflow-hidden">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Today's Sales</p>
                    <p class="text-xl font-extrabold text-white mt-1.5" id="statTodaySales">Rs. 0.00</p>
                    <div class="absolute bottom-2 right-3 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-lime">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h.007v.008H3.75V4.5Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3 10.5h18M3 16.5h18M4 5.25h16.25a.75.75 0 0 1 .75.75v10.5a.75.75 0 0 1-.75.75H4a.75.75 0 0 1-.75-.75V6a.75.75 0 0 1 .75-.75Zm7.5 12h.008v.008H11.5v-.008Zm1.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                    </div>
                </div>

                <!-- Total Profit -->
                <div class="bg-dark-900/60 border border-white/10 p-4 rounded-3xl relative overflow-hidden">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Total Profit</p>
                    <p class="text-xl font-extrabold text-lime mt-1.5" id="statTotalProfit">Rs. 0.00</p>
                    <div class="absolute bottom-2 right-3 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-lime">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
                        </svg>
                    </div>
                </div>

                <!-- Remaining Stock Value -->
                <div class="col-span-2 bg-dark-900/60 border border-white/10 p-4 rounded-3xl relative overflow-hidden">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Remaining Stock Value</p>
                    <p class="text-xl font-extrabold text-white mt-1.5" id="statStockValue">Rs. 0.00</p>
                    <div class="absolute bottom-2 right-3 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-12 h-12 text-lime">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Partner Profit Split Display -->
            <div class="bg-dark-900/60 border border-white/10 p-4 rounded-3xl relative overflow-hidden">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">50 / 50 Partner Profit Share</p>
                <div class="flex items-center justify-between mt-2.5">
                    <div>
                        <p class="text-xs text-gray-500 font-bold">Each Share Value</p>
                        <p class="text-2xl font-black text-lime" id="statProfitSplit">Rs. 0.00</p>
                    </div>
                    <div class="flex gap-2">
                        <div class="w-8 h-8 rounded-full bg-lime/10 border border-lime/30 flex items-center justify-center font-extrabold text-lime text-xs">S</div>
                        <div class="w-8 h-8 rounded-full bg-white/10 border border-white/20 flex items-center justify-center font-extrabold text-white text-xs">U</div>
                    </div>
                </div>
            </div>

            <!-- Manual PWA Install Prompt Button -->
            <div id="pwaInstallContainer" class="hidden">
                <button onclick="triggerPwaInstall()" class="w-full bg-dark-900 border border-white/10 hover:border-lime/40 text-lime font-bold py-3.5 rounded-2xl text-xs tap-scale flex items-center justify-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>Install VX Standalone App</span>
                </button>
            </div>

            <!-- New Sale Action Button -->
            <div class="mt-4">
                <button onclick="showTab('pos')" class="w-full bg-lime text-black font-extrabold py-4 rounded-2xl text-sm tracking-wide tap-scale flex items-center justify-center gap-2 shadow-lg shadow-lime/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>New Sale</span>
                </button>
            </div>

            <!-- Catalog View & Share Buttons -->
            <div class="grid grid-cols-2 gap-2.5 mt-2.5">
                <button onclick="window.open('catalog.php', '_blank')" class="bg-dark-800 border border-white/10 font-bold py-3.5 rounded-2xl text-xs flex items-center justify-center gap-2 tap-scale hover:border-lime/40 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-lime">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    <span>View Catalog</span>
                </button>
                <button onclick="shareCatalog()" class="bg-dark-800 border border-white/10 font-bold py-3.5 rounded-2xl text-xs flex items-center justify-center gap-2 tap-scale hover:border-lime/40 text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-lime">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                    </svg>
                    <span>Share</span>
                </button>
            </div>

            <!-- Recent Sales Section -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Recent Transactions</h4>
                    <button onclick="showTab('sales')" class="text-xs text-lime font-bold hover:underline">View All</button>
                </div>
                <div class="space-y-2.5" id="dashboardRecentSalesList">
                    <!-- Loaded dynamically via AJAX -->
                    <div class="text-center py-6 text-xs text-gray-500 font-semibold">Loading stats...</div>
                </div>
            </div>
        </section>

        <!-- ==================== POS BILLING TAB ==================== -->
        <section id="tab-pos" class="hidden space-y-4">
            <!-- Section Header -->
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold text-white">POS Terminal</h2>
                <div class="bg-lime/10 text-lime text-xs font-bold px-3 py-1 rounded-full flex items-center gap-1.5">
                    <span class="w-2 h-2 bg-lime rounded-full"></span> Fast One-Hand Billing
                </div>
            </div>

            <!-- Instant Search Input -->
            <div class="relative bg-dark-900 border border-white/10 rounded-2xl p-1 neon-border transition-all flex items-center">
                <div class="pl-3.5 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="posSearchInput" 
                    oninput="filterPOSProducts(this.value)" 
                    placeholder="Search product name instantly..." 
                    class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-sm font-semibold p-2.5"
                >
            </div>

            <!-- Horizontal Scrollable Product Carousel -->
            <div class="space-y-1.5">
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider">Fast Access Products</p>
                <div class="flex gap-2.5 overflow-x-auto no-scrollbar py-1" id="posCarouselProducts">
                    <!-- Dynamically populated carousel -->
                </div>
            </div>

            <!-- Main Product List / Grid -->
            <div>
                <p class="text-[10px] text-gray-400 font-semibold uppercase tracking-wider mb-2.5">All Available Stock</p>
                <div class="grid grid-cols-2 gap-3" id="posProductsGrid">
                    <!-- Loaded dynamically via AJAX -->
                    <div class="col-span-2 text-center py-10 text-xs text-gray-500">Loading products...</div>
                </div>
            </div>
        </section>

        <!-- ==================== INVENTORY TAB ==================== -->
        <section id="tab-inventory" class="hidden space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">Stock Room</h2>
                    <p class="text-xs text-gray-400 font-semibold">Active & Inactive Inventories</p>
                </div>
                <button onclick="openProductModal()" class="bg-lime text-black text-xs font-bold py-2.5 px-4 rounded-xl flex items-center gap-1.5 tap-scale">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>New Item</span>
                </button>
            </div>

            <!-- Search Field -->
            <div class="relative bg-dark-900 border border-white/10 rounded-2xl p-1 neon-border transition-all flex items-center">
                <div class="pl-3.5 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="inventorySearchInput" 
                    oninput="filterInventoryProducts(this.value)" 
                    placeholder="Search stock list..." 
                    class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-sm font-semibold p-2.5"
                >
            </div>

            <!-- Products List -->
            <div class="space-y-3" id="inventoryProductsList">
                <!-- Loaded dynamically via AJAX -->
                <div class="text-center py-10 text-xs text-gray-500 font-semibold">Loading stock list...</div>
            </div>
        </section>

        <!-- ==================== SALES TAB ==================== -->
        <section id="tab-sales" class="hidden space-y-4">
            <h2 class="text-xl font-bold text-white">Sales Ledger</h2>

            <!-- Search / Filter Accordion -->
            <div class="bg-dark-900/60 border border-white/10 p-3.5 rounded-3xl space-y-3">
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Reports & Ledger Filters</p>
                <div class="grid grid-cols-2 gap-2.5">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-500 tracking-wide uppercase">Start Date</label>
                        <input type="date" id="filterStartDate" class="bg-dark-800 border border-white/10 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-lime w-full font-semibold">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-500 tracking-wide uppercase">End Date</label>
                        <input type="date" id="filterEndDate" class="bg-dark-800 border border-white/10 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-lime w-full font-semibold">
                    </div>
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-500 tracking-wide uppercase">Selling Partner</label>
                    <select id="filterPartner" class="bg-dark-800 border border-white/10 rounded-xl p-2.5 text-xs text-white focus:outline-none focus:ring-1 focus:ring-lime w-full font-semibold">
                        <option value="">All Partners</option>
                        <option value="1">Susara Senarathne</option>
                        <option value="2">Umesha Udayanga</option>
                    </select>
                </div>
                <button onclick="applySalesFilters()" class="w-full bg-lime text-black font-extrabold py-2.5 rounded-xl text-xs tap-scale hover:opacity-95 flex items-center justify-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.637 10.637Z" />
                    </svg>
                    <span>Run Report & Filter</span>
                </button>
            </div>

            <!-- Ledger Performance Tabs -->
            <div class="flex border-b border-white/10">
                <button onclick="switchSalesSubTab('list')" id="subtab-btn-list" class="flex-1 py-3 text-xs font-bold border-b-2 border-lime text-lime focus:outline-none">Ledger Items</button>
                <button onclick="switchSalesSubTab('daily')" id="subtab-btn-daily" class="flex-1 py-3 text-xs font-bold border-b-2 border-transparent text-gray-400 focus:outline-none">Daily Stats</button>
                <button onclick="switchSalesSubTab('monthly')" id="subtab-btn-monthly" class="flex-1 py-3 text-xs font-bold border-b-2 border-transparent text-gray-400 focus:outline-none">Monthly Stats</button>
            </div>

            <!-- Filter Totals Alert -->
            <div class="bg-dark-900 border border-white/10 p-3.5 rounded-2xl flex justify-between text-xs">
                <div>
                    <span class="text-gray-500 font-bold block">Selected Sales</span>
                    <span class="text-sm font-extrabold text-white" id="salesFilteredTotal">Rs. 0.00</span>
                </div>
                <div class="text-right border-l border-white/10 pl-6">
                    <span class="text-gray-500 font-bold block">Selected Profit</span>
                    <span class="text-sm font-extrabold text-lime" id="salesFilteredProfit">Rs. 0.00</span>
                </div>
            </div>

            <!-- Sales list view -->
            <div id="sales-subtab-list" class="space-y-3">
                <!-- Dynamically rendered -->
            </div>

            <!-- Daily summary report -->
            <div id="sales-subtab-daily" class="hidden space-y-2.5">
                <!-- Dynamically rendered table -->
            </div>

            <!-- Monthly summary report -->
            <div id="sales-subtab-monthly" class="hidden space-y-2.5">
                <!-- Dynamically rendered table -->
            </div>
        </section>

        <!-- ==================== ACCOUNTS TAB ==================== -->
        <section id="tab-accounts" class="hidden space-y-5">
            <h2 class="text-xl font-bold text-white">Accounts Office</h2>

            <!-- Partner Accounts list -->
            <div class="space-y-4" id="accountsList">
                <!-- Loaded dynamically via AJAX -->
            </div>

            <!-- Partnership Summary Sheet -->
            <div class="bg-dark-900 border border-white/10 p-5 rounded-3xl space-y-4 relative overflow-hidden">
                <div class="absolute -top-12 -right-12 w-28 h-28 bg-lime/10 rounded-full blur-2xl pointer-events-none"></div>
                
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-white/5 pb-2 flex items-center justify-between">
                    <span>Partnership Capital Ledger</span>
                    <span class="text-lime text-[10px]">50/50 Equity</span>
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-400">Accumulated Retained Profits:</span>
                        <span class="text-lime font-bold" id="acctTotalProfit">Rs. 0.00</span>
                    </div>
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-400">Total Cash Held (Partners):</span>
                        <span class="text-white" id="acctTotalCashInHand">Rs. 0.00</span>
                    </div>
                    <div class="flex justify-between font-semibold">
                        <span class="text-gray-400">Total Stock Inventory Cost Value:</span>
                        <span class="text-white" id="acctRemainingStockValue">Rs. 0.00</span>
                    </div>
                    <div class="border-t border-white/5 mt-1.5 pt-1.5 flex justify-between font-bold">
                        <span class="text-white">Sum of Current Assets:</span>
                        <span class="text-lime" id="acctAssetsSum">Rs. 0.00</span>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <!-- FIXED MOBILE SLIDE-UP CART BAR -->
    <div id="cartBar" class="fixed bottom-[4.5rem] left-0 right-0 z-30 max-w-md mx-auto px-4 translate-y-32 transition-transform duration-300 pointer-events-none">
        <div onclick="toggleCartDrawer(true)" class="bg-lime text-black p-4 rounded-2xl flex items-center justify-between shadow-2xl neon-shadow pointer-events-auto cursor-pointer tap-scale font-extrabold text-sm">
            <div class="flex items-center gap-3">
                <div class="bg-black/10 px-2.5 py-1 rounded-lg text-xs" id="cartCountBadge">0 Items</div>
                <div class="text-xs">
                    <p class="text-[9px] uppercase tracking-wider text-black/50 font-black">Estimated Profit</p>
                    <p id="cartProfitBadge" class="text-xs font-black">Rs. 0.00</p>
                </div>
            </div>
            <div class="flex items-center gap-1.5">
                <span>View Cart Total: <span id="cartTotalBadge">Rs. 0</span></span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                </svg>
            </div>
        </div>
    </div>

    <!-- MOBILE BOTTOM NAVIGATION -->
    <nav class="bottom-nav fixed bottom-0 left-0 right-0 z-40 bg-dark-900/90 backdrop-blur-xl border-t border-white/5 max-w-md mx-auto flex justify-between px-3.5 py-2 select-none shadow-2xl">
        <button onclick="showTab('dashboard')" id="nav-btn-dashboard" class="flex-1 py-1.5 flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-lime transition-all focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
            </svg>
            <span>Home</span>
        </button>
        <button onclick="showTab('pos')" id="nav-btn-pos" class="flex-1 py-1.5 flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-500 transition-all focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            <span>POS Bill</span>
        </button>
        <button onclick="showTab('inventory')" id="nav-btn-inventory" class="flex-1 py-1.5 flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-500 transition-all focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>
            <span>Stock</span>
        </button>
        <button onclick="showTab('sales')" id="nav-btn-sales" class="flex-1 py-1.5 flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-500 transition-all focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
            </svg>
            <span>Sales</span>
        </button>
        <button onclick="showTab('accounts')" id="nav-btn-accounts" class="flex-1 py-1.5 flex flex-col items-center justify-center gap-1 text-[10px] font-bold text-gray-500 transition-all focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.33l-7.5-5-7.5 5V21" />
            </svg>
            <span>Accounts</span>
        </button>
    </nav>


    <!-- ==================== MODALS & POPUPS ==================== -->

    <!-- CUSTOM TOAST NOTIFICATIONS -->
    <div id="toastContainer" class="fixed top-4 left-4 right-4 z-50 pointer-events-none flex flex-col items-center gap-2"></div>

    <!-- POS ITEM CONFIGURATION MODAL (QUANTITY / PRICE ADJUSTMENT) -->
    <div id="posItemModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-end justify-center p-4">
        <div class="w-full max-w-md bg-dark-900 border border-white/10 rounded-t-3xl sm:rounded-3xl p-6 space-y-4 relative overflow-hidden animate-slide-up">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-lime/10 rounded-full blur-2xl"></div>
            
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-white" id="posModalProdName">Product Name</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5">Available Stock: <span id="posModalProdStock" class="text-gray-300 font-bold">0</span></p>
                </div>
                <button onclick="closePOSModal()" class="text-gray-400 hover:text-white bg-dark-800 p-2 rounded-xl border border-white/5 tap-scale">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="space-y-4 pt-2">
                <!-- Customized price (For current bill ONLY) -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-400 tracking-wide uppercase">Selling Price (This Bill Only)</label>
                    <div class="flex items-center bg-dark-800 border border-white/10 rounded-2xl p-3.5 neon-border transition-all">
                        <span class="text-sm font-bold text-gray-500 mr-2">Rs.</span>
                        <input 
                            type="number" 
                            step="0.01"
                            id="posModalPriceInput" 
                            class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-sm font-bold"
                            placeholder="0.00"
                        >
                    </div>
                </div>

                <!-- Quantity input with tap counters -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-400 tracking-wide uppercase">Billing Quantity</label>
                    <div class="flex items-center justify-between bg-dark-800 border border-white/10 rounded-2xl p-2">
                        <button onclick="adjustPOSModalQty(-1)" class="w-12 h-12 bg-dark-700 rounded-xl text-white font-extrabold text-xl flex items-center justify-center tap-scale">-</button>
                        <input 
                            type="number" 
                            id="posModalQtyInput" 
                            class="bg-transparent border-0 w-20 text-center text-white focus:outline-none focus:ring-0 text-lg font-bold"
                            value="1"
                            min="1"
                        >
                        <button onclick="adjustPOSModalQty(1)" class="w-12 h-12 bg-dark-700 rounded-xl text-white font-extrabold text-xl flex items-center justify-center tap-scale">+</button>
                    </div>
                </div>
            </div>

            <div class="pt-3">
                <button id="posModalAddBtn" class="w-full bg-lime text-black font-extrabold py-4 rounded-2xl text-sm tracking-wide tap-scale flex items-center justify-center gap-2">
                    <span>Add to Bill</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- SLIDE-UP CART DETAIL VIEW DRAWER -->
    <div id="cartDrawer" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-end justify-center">
        <div class="w-full max-w-md bg-dark-900 border-t border-white/10 rounded-t-3xl max-h-[85vh] flex flex-col relative overflow-hidden drawer-transition translate-y-full">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-lime/10 rounded-full blur-2xl"></div>
            
            <!-- Header -->
            <div class="p-5 border-b border-white/5 flex items-center justify-between shrink-0">
                <div>
                    <h3 class="text-base font-bold text-white">Your Current Bill</h3>
                    <p class="text-xs text-gray-500 font-semibold mt-0.5" id="cartDrawerCount">0 items active</p>
                </div>
                <button onclick="toggleCartDrawer(false)" class="text-gray-400 hover:text-white bg-dark-800 p-2 rounded-xl border border-white/5 tap-scale">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                </button>
            </div>

            <!-- Cart items list (Scrollable) -->
            <div class="flex-1 overflow-y-auto p-4 space-y-3 no-scrollbar" id="cartDrawerItemsList">
                <!-- Loaded dynamically -->
            </div>

            <!-- Footer summary -->
            <div class="p-5 bg-dark-950/90 border-t border-white/5 space-y-4 shrink-0">
                <div class="space-y-1.5">
                    <div class="flex justify-between text-xs font-semibold text-gray-400">
                        <span>Total Profits Captured:</span>
                        <span id="cartDrawerProfit" class="text-lime">Rs. 0.00</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <span class="text-xs font-extrabold text-white uppercase tracking-wider">Subtotal:</span>
                        <span class="text-2xl font-black text-white" id="cartDrawerTotal">Rs. 0.00</span>
                    </div>
                </div>

                <div class="flex gap-2.5">
                    <button onclick="clearCart()" class="bg-dark-800 border border-white/10 text-red-400 font-bold px-4 py-4 rounded-2xl text-sm tap-scale flex items-center justify-center" title="Clear Bill">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                    <button onclick="submitPOSSale()" class="flex-1 bg-lime text-black font-black py-4 rounded-2xl text-sm tracking-wide tap-scale flex items-center justify-center gap-2 shadow-lg shadow-lime/20">
                        <span>Save & Complete Sale</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- PRODUCT CREATION & EDITION MODAL -->
    <div id="productFormModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden items-center justify-center p-4">
        <div class="w-full max-w-md bg-dark-900 border border-white/10 rounded-3xl p-6 space-y-4 relative overflow-hidden animate-scale">
            <div class="absolute -top-12 -right-12 w-24 h-24 bg-lime/10 rounded-full blur-2xl"></div>
            
            <div class="flex justify-between items-center">
                <h3 class="text-base font-bold text-white" id="productModalTitle">Add Product</h3>
                <button onclick="closeProductModal()" class="text-gray-400 hover:text-white bg-dark-800 p-2 rounded-xl border border-white/5 tap-scale">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form id="productForm" onsubmit="saveProduct(event)" class="space-y-4" enctype="multipart/form-data">
                <input type="hidden" name="id" id="prodId">
                
                <!-- Product Name -->
                <div class="space-y-1.5">
                    <label for="prodName" class="text-xs font-bold text-gray-400 tracking-wide uppercase">Product Name</label>
                    <div class="flex items-center bg-dark-800 border border-white/10 rounded-xl p-3 neon-border transition-all">
                        <input type="text" name="name" id="prodName" required placeholder="e.g. Premium Rubber Seal" class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-semibold">
                    </div>
                </div>

                <!-- Puff Count -->
                <div class="space-y-1.5">
                    <label for="prodPuff" class="text-xs font-bold text-gray-400 tracking-wide uppercase">Puff Count (Optional)</label>
                    <div class="flex items-center bg-dark-800 border border-white/10 rounded-xl p-3 neon-border transition-all">
                        <input type="number" name="puff" id="prodPuff" placeholder="e.g. 5000, 20000" class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-semibold font-bold">
                    </div>
                </div>

                <!-- Buying Price & Selling Price -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1.5">
                        <label for="prodBuyingPrice" class="text-xs font-bold text-gray-400 tracking-wide uppercase">Buying Price</label>
                        <div class="flex items-center bg-dark-800 border border-white/10 rounded-xl p-3 neon-border transition-all">
                            <span class="text-xs font-bold text-gray-500 mr-1.5">Rs.</span>
                            <input type="number" step="0.01" name="buying_price" id="prodBuyingPrice" required placeholder="0.00" class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-bold">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label for="prodSellingPrice" class="text-xs font-bold text-gray-400 tracking-wide uppercase">Selling Price</label>
                        <div class="flex items-center bg-dark-800 border border-white/10 rounded-xl p-3 neon-border transition-all">
                            <span class="text-xs font-bold text-gray-500 mr-1.5">Rs.</span>
                            <input type="number" step="0.01" name="selling_price" id="prodSellingPrice" required placeholder="0.00" class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-bold">
                        </div>
                    </div>
                </div>

                <!-- Stock Quantity -->
                <div class="space-y-1.5">
                    <label for="prodStock" class="text-xs font-bold text-gray-400 tracking-wide uppercase">Stock Quantity</label>
                    <div class="flex items-center bg-dark-800 border border-white/10 rounded-xl p-3 neon-border transition-all">
                        <input type="number" name="stock_quantity" id="prodStock" required placeholder="e.g. 50" class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-bold">
                    </div>
                </div>

                <!-- Image Upload with Preview -->
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-400 tracking-wide uppercase">Product Image</label>
                    <div class="flex items-center gap-3">
                        <label class="flex-1 flex flex-col items-center justify-center border border-dashed border-white/20 rounded-xl p-4 bg-dark-800 cursor-pointer hover:border-lime/40 transition-all text-xs font-bold text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-lime mb-1">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                            <span>Browse Image</span>
                            <input type="file" name="image" id="prodImage" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </label>
                        <div id="imagePreviewContainer" class="w-16 h-16 rounded-xl border border-white/10 bg-dark-800 flex items-center justify-center text-[10px] text-gray-500 font-bold overflow-hidden shrink-0">
                            No Image
                        </div>
                    </div>
                    <!-- Upload Progress Bar -->
                    <div id="uploadProgressWrapper" class="hidden w-full bg-dark-950 border border-white/5 rounded-xl p-2.5 space-y-1.5 mt-2">
                        <div class="flex justify-between items-center text-[10px] font-bold">
                            <span class="text-gray-500 uppercase tracking-wider">Uploading Image...</span>
                            <span id="uploadProgressPercent" class="text-lime">0%</span>
                        </div>
                        <div class="w-full bg-dark-800 rounded-full h-1.5 overflow-hidden">
                            <div id="uploadProgressBar" class="bg-lime h-1.5 transition-all duration-150" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <!-- Status Checkbox -->
                <div class="flex items-center justify-between border-t border-white/5 pt-3">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">Status Active</span>
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" name="status" id="prodStatus" value="1" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-dark-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:start-[2px] after:bg-gray-400 after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-lime peer-checked:after:bg-black peer-checked:after:border-black"></div>
                    </label>
                </div>

                <!-- Save button -->
                <button type="submit" class="w-full bg-lime text-black font-extrabold py-3.5 rounded-xl text-xs tracking-wide tap-scale flex items-center justify-center gap-1.5 shadow-lg shadow-lime/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span>Save Product Details</span>
                </button>
            </form>
        </div>
    </div>




    <!-- ==================== CLIENT JAVASCRIPT ==================== -->
    <script>
        // GLOBALS & STATE
        let activeTab = 'dashboard';
        let productsStore = [];     // Cached product list
        let activeSaleSubTab = 'list';
        let currentUserId = <?= (int)$current_user_id ?>;
        
        // POS Cart state
        let cart = [];
        let posSelectedProduct = null;

        // ON PAGE LOAD
        document.addEventListener('DOMContentLoaded', () => {
            // Initial boot
            showTab('dashboard');
            
            // Set current date values in sales filters
            const todayStr = new Date().toISOString().split('T')[0];
            document.getElementById('filterStartDate').value = todayStr;
            document.getElementById('filterEndDate').value = todayStr;
        });

        // TOAST NOTIFICATIONS HELPER
        function showNotification(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            
            const bgClass = type === 'success' ? 'bg-dark-900 border-lime/30' : 'bg-dark-900 border-red-500/30';
            const iconColor = type === 'success' ? 'text-lime' : 'text-red-500';
            const iconSvg = type === 'success' 
                ? `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 ${iconColor}"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 ${iconColor}"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 7.5h.008v.008H12v-.008Z" /></svg>`;

            toast.className = `p-3 rounded-2xl border ${bgClass} shadow-xl flex items-center gap-3.5 pointer-events-auto transition-all duration-300 transform scale-90 translate-y-2 opacity-0 max-w-sm w-full`;
            toast.innerHTML = `
                <div class="bg-dark-800 p-1.5 rounded-lg">${iconSvg}</div>
                <p class="text-xs font-semibold text-white flex-1">${message}</p>
            `;
            
            container.appendChild(toast);
            
            // Trigger animation in
            setTimeout(() => {
                toast.classList.remove('scale-90', 'translate-y-2', 'opacity-0');
            }, 10);
            
            // Trigger remove
            setTimeout(() => {
                toast.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }

        // TAB NAVIGATION ROUTER
        function showTab(tabName) {
            // Hide all tab content sections
            document.querySelectorAll('main > section').forEach(sec => sec.classList.add('hidden'));
            
            // Deactivate all bottom nav buttons
            document.querySelectorAll('nav > button').forEach(btn => {
                btn.classList.remove('text-lime');
                btn.classList.add('text-gray-500');
            });
            
            // Show requested tab content
            document.getElementById(`tab-${tabName}`).classList.remove('hidden');
            
            // Set active state on corresponding nav button
            const navBtn = document.getElementById(`nav-btn-${tabName}`);
            if (navBtn) {
                navBtn.classList.remove('text-gray-500');
                navBtn.classList.add('text-lime');
            }
            
            activeTab = tabName;
            
            // Perform tab-specific refreshes
            if (tabName === 'dashboard') {
                loadDashboardStats();
            } else if (tabName === 'pos') {
                loadPOSProducts();
            } else if (tabName === 'inventory') {
                loadInventoryProducts();
            } else if (tabName === 'sales') {
                loadSales();
            } else if (tabName === 'accounts') {
                loadAccounts();
            }
        }

        // ==================== DASHBOARD OPERATIONS ====================
        async function loadDashboardStats() {
            try {
                const res = await fetch('api.php?action=get_dashboard_stats');
                const data = await res.json();
                
                if (data.success) {
                    // Update stats counters
                    document.getElementById('statTodaySales').innerText = `Rs. ${parseFloat(data.stats.today_sales).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('statTotalProfit').innerText = `Rs. ${parseFloat(data.stats.total_profit).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('statStockValue').innerText = `Rs. ${parseFloat(data.stats.remaining_stock_value).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    document.getElementById('statProfitSplit').innerText = `Rs. ${parseFloat(data.stats.partner_profit_split).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    
                    // Update top navbar cash balance
                    document.getElementById('topUserCashBalance').innerText = `Rs. ${parseFloat(data.stats.user_cash_balance).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
                    
                    // Render recent sales
                    const listContainer = document.getElementById('dashboardRecentSalesList');
                    if (data.recent_sales.length === 0) {
                        listContainer.innerHTML = '<div class="text-center py-6 text-xs text-gray-500 font-bold">No sales recorded yet.</div>';
                        return;
                    }
                    
                    listContainer.innerHTML = data.recent_sales.map(s => {
                        const date = new Date(s.created_at);
                        const timeStr = date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                        return `
                            <div class="bg-dark-900 border border-white/5 p-3.5 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-lime/10 border border-lime/20 flex items-center justify-center font-black text-lime text-xs tracking-wider">
                                        +
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-white">Sale #00${s.id}</p>
                                        <p class="text-[10px] text-gray-500 font-semibold">${s.partner_name} • ${timeStr}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-black text-white">Rs. ${parseFloat(s.total_amount).toFixed(2)}</p>
                                    <p class="text-[9px] text-lime font-bold">Profit: +Rs. ${parseFloat(s.total_profit).toFixed(2)}</p>
                                </div>
                            </div>
                        `;
                    }).join('');
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showNotification('Connection failure while loading dashboard.', 'error');
            }
        }

        // ==================== POS SYSTEM OPERATIONS ====================
        async function loadPOSProducts(search = '') {
            try {
                const res = await fetch(`api.php?action=get_products&search=${encodeURIComponent(search)}`);
                const data = await res.json();
                
                if (data.success) {
                    productsStore = data.products;
                    
                    // 1. Populate the Horizontal scrolling carousel (Fast Access active products)
                    const carouselContainer = document.getElementById('posCarouselProducts');
                    const activeProducts = productsStore.filter(p => p.status == 1);
                    
                    if (activeProducts.length === 0) {
                        carouselContainer.innerHTML = '<span class="text-xs text-gray-500 font-bold py-2">No active stock.</span>';
                    } else {
                        carouselContainer.innerHTML = activeProducts.map(p => {
                            const imgHtml = p.image_path 
                                ? `<img src="${p.image_path}" class="w-full h-full object-cover">`
                                : `<span class="font-extrabold text-[10px] text-lime">${p.name.substring(0,2).toUpperCase()}</span>`;
                            return `
                                <button onclick="openPOSModalById(${p.id})" class="flex-shrink-0 bg-dark-900 border border-white/5 p-2 rounded-2xl w-24 flex flex-col items-center text-center tap-scale">
                                    <div class="w-10 h-10 rounded-xl bg-dark-800 border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                                        ${imgHtml}
                                    </div>
                                    <p class="text-[10px] font-bold text-white truncate w-full mt-1.5">${p.name}</p>
                                    <p class="text-[9px] font-semibold text-lime mt-0.5">Rs. ${parseFloat(p.selling_price).toFixed(0)}</p>
                                </button>
                            `;
                        }).join('');
                    }

                    // 2. Render Main POS Grid
                    renderPOSGrid(activeProducts);
                }
            } catch (err) {
                console.error(err);
                showNotification('Error retrieving POS products.', 'error');
            }
        }

        function renderPOSGrid(list) {
            const gridContainer = document.getElementById('posProductsGrid');
            if (list.length === 0) {
                gridContainer.innerHTML = '<div class="col-span-2 text-center py-10 text-xs text-gray-500 font-bold">No active matching products found.</div>';
                return;
            }

            gridContainer.innerHTML = list.map(p => {
                const imgHtml = p.image_path 
                    ? `<img src="${p.image_path}" class="w-full h-full object-cover">`
                    : `<span class="font-black text-lime text-base">${p.name.substring(0,2).toUpperCase()}</span>`;
                const qtyBadgeColor = p.stock_quantity > 0 ? 'bg-dark-800 text-gray-400 border-white/5' : 'bg-red-950/40 text-red-400 border-red-500/20';
                return `
                    <div onclick="${p.stock_quantity > 0 ? `openPOSModalById(${p.id})` : "showNotification('Item is out of stock!','error')"}" class="bg-dark-900 border border-white/5 p-3 rounded-2xl flex flex-col justify-between tap-scale cursor-pointer hover:border-lime/20 relative overflow-hidden">
                        <div class="aspect-square w-full rounded-xl bg-dark-800 border border-white/10 flex items-center justify-center overflow-hidden mb-2.5 relative">
                            ${imgHtml}
                            ${p.puff ? `<div class="absolute top-1.5 left-1.5 bg-black/75 backdrop-blur-md border border-white/10 text-[8px] font-extrabold text-lime px-2 py-0.5 rounded-lg select-none tracking-wider">${p.puff} Puffs</div>` : ''}
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xs font-bold text-white truncate">${p.name}</h4>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-black text-lime">Rs. ${parseFloat(p.selling_price).toFixed(2)}</span>
                                <span class="text-[9px] font-semibold border px-1.5 py-0.5 rounded ${qtyBadgeColor}">Stock: ${p.stock_quantity}</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Live filter POS
        function filterPOSProducts(query) {
            const searchLower = query.toLowerCase().trim();
            const filtered = productsStore.filter(p => p.status == 1 && p.name.toLowerCase().includes(searchLower));
            renderPOSGrid(filtered);
        }

        // Trigger POS modal by matching ID
        function openPOSModalById(id) {
            const prod = productsStore.find(p => p.id == id);
            if (prod) openPOSModal(prod);
        }

        // POS modal handling
        function openPOSModal(product) {
            posSelectedProduct = product;
            document.getElementById('posModalProdName').innerText = product.name;
            document.getElementById('posModalProdStock').innerText = product.stock_quantity;
            document.getElementById('posModalPriceInput').value = parseFloat(product.selling_price);
            document.getElementById('posModalQtyInput').value = 1;
            
            // Check if item is already in cart, if so load details
            const existingItem = cart.find(c => c.id == product.id);
            if (existingItem) {
                document.getElementById('posModalPriceInput').value = parseFloat(existingItem.selling_price);
                document.getElementById('posModalQtyInput').value = existingItem.quantity;
            }

            const modal = document.getElementById('posItemModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            
            // Event listener for Submit button
            document.getElementById('posModalAddBtn').onclick = () => {
                const qty = parseInt(document.getElementById('posModalQtyInput').value);
                const price = parseFloat(document.getElementById('posModalPriceInput').value);
                
                if (isNaN(qty) || qty <= 0) {
                    showNotification('Please enter a valid quantity', 'error');
                    return;
                }
                if (qty > product.stock_quantity) {
                    showNotification(`Insufficient stock. Maximum available: ${product.stock_quantity}`, 'error');
                    return;
                }
                if (isNaN(price) || price < 0) {
                    showNotification('Please enter a valid price', 'error');
                    return;
                }

                addToCart(product, qty, price);
                closePOSModal();
            };
        }

        function closePOSModal() {
            document.getElementById('posItemModal').classList.add('hidden');
            document.getElementById('posItemModal').classList.remove('flex');
            posSelectedProduct = null;
        }

        function adjustPOSModalQty(delta) {
            const input = document.getElementById('posModalQtyInput');
            let current = parseInt(input.value) || 1;
            current += delta;
            if (current < 1) current = 1;
            
            if (posSelectedProduct && current > posSelectedProduct.stock_quantity) {
                showNotification(`Only ${posSelectedProduct.stock_quantity} available in inventory`, 'warning');
                current = posSelectedProduct.stock_quantity;
            }
            input.value = current;
        }

        // Cart details controller
        function addToCart(product, quantity, sellingPrice) {
            const existingIndex = cart.findIndex(c => c.id == product.id);
            
            if (existingIndex > -1) {
                cart[existingIndex].quantity = quantity;
                cart[existingIndex].selling_price = sellingPrice;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    image_path: product.image_path,
                    buying_price: parseFloat(product.buying_price),
                    selling_price: sellingPrice,
                    quantity: quantity,
                    max_stock: product.stock_quantity
                });
            }
            
            showNotification(`Added ${product.name} to bill`, 'success');
            updateCartUI();
        }

        function updateCartUI() {
            const count = cart.reduce((acc, item) => acc + item.quantity, 0);
            const total = cart.reduce((acc, item) => acc + (item.selling_price * item.quantity), 0);
            const totalProfit = cart.reduce((acc, item) => acc + ((item.selling_price - item.buying_price) * item.quantity), 0);

            // Update bottom bar badge
            const cartBar = document.getElementById('cartBar');
            if (count > 0) {
                cartBar.classList.remove('translate-y-32');
                document.getElementById('cartCountBadge').innerText = `${count} Item${count > 1 ? 's' : ''}`;
                document.getElementById('cartTotalBadge').innerText = `Rs. ${total.toFixed(0)}`;
                document.getElementById('cartProfitBadge').innerText = `Rs. ${totalProfit.toFixed(2)}`;
            } else {
                cartBar.classList.add('translate-y-32');
            }

            // Update Drawer totals
            document.getElementById('cartDrawerCount').innerText = `${cart.length} active row${cart.length !== 1 ? 's' : ''}`;
            document.getElementById('cartDrawerTotal').innerText = `Rs. ${total.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('cartDrawerProfit').innerText = `Rs. ${totalProfit.toLocaleString('en-US', {minimumFractionDigits: 2})}`;

            // Populate Drawer Items
            const itemsList = document.getElementById('cartDrawerItemsList');
            if (cart.length === 0) {
                itemsList.innerHTML = '<div class="text-center py-10 text-xs text-gray-500 font-bold">Your bill is empty.</div>';
                return;
            }

            itemsList.innerHTML = cart.map((item, idx) => {
                const imgHtml = item.image_path 
                    ? `<img src="${item.image_path}" class="w-full h-full object-cover">`
                    : `<span class="font-extrabold text-[10px] text-lime">${item.name.substring(0,2).toUpperCase()}</span>`;
                const itemTotal = item.selling_price * item.quantity;
                const itemProfit = (item.selling_price - item.buying_price) * item.quantity;

                return `
                    <div class="bg-dark-800 border border-white/5 p-3 rounded-2xl flex items-center justify-between gap-3">
                        <div class="w-10 h-10 rounded-xl bg-dark-700 border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                            ${imgHtml}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-xs font-bold text-white truncate">${item.name}</h4>
                            <p class="text-[10px] text-lime font-bold mt-0.5">Profit: Rs. ${itemProfit.toFixed(2)}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[10px] text-gray-400 font-bold">Rs. ${parseFloat(item.selling_price).toFixed(2)} x</span>
                                <input 
                                    type="number" 
                                    class="bg-dark-950 border border-white/10 rounded px-1.5 py-0.5 text-center text-white focus:outline-none w-10 text-[10px] font-bold"
                                    value="${item.quantity}"
                                    min="1"
                                    max="${item.max_stock}"
                                    onchange="changeCartQty(${idx}, this.value)"
                                >
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <p class="text-xs font-black text-white">Rs. ${itemTotal.toFixed(2)}</p>
                            <button onclick="removeFromCart(${idx})" class="text-red-400 hover:text-red-300 p-1 bg-red-950/20 border border-red-500/10 rounded-lg tap-scale">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function changeCartQty(idx, val) {
            let qty = parseInt(val);
            if (isNaN(qty) || qty <= 0) qty = 1;
            
            const max = cart[idx].max_stock;
            if (qty > max) {
                showNotification(`Maximum available is ${max}`, 'warning');
                qty = max;
            }
            cart[idx].quantity = qty;
            updateCartUI();
        }

        function removeFromCart(idx) {
            cart.splice(idx, 1);
            showNotification('Item removed from bill', 'info');
            updateCartUI();
        }

        function clearCart() {
            if (confirm('Clear the current billing drawer?')) {
                cart = [];
                updateCartUI();
                toggleCartDrawer(false);
            }
        }

        function toggleCartDrawer(open) {
            const drawer = document.getElementById('cartDrawer');
            const drawerContent = drawer.querySelector('.drawer-transition');
            
            if (open) {
                drawer.classList.remove('hidden');
                drawer.classList.add('flex');
                setTimeout(() => {
                    drawerContent.classList.remove('translate-y-full');
                }, 10);
            } else {
                drawerContent.classList.add('translate-y-full');
                setTimeout(() => {
                    drawer.classList.add('hidden');
                    drawer.classList.remove('flex');
                }, 300);
            }
        }

        async function submitPOSSale() {
            if (cart.length === 0) return;

            try {
                const formData = new FormData();
                formData.append('cart', JSON.stringify(cart));

                const res = await fetch('api.php?action=complete_sale', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showNotification('Sale completed successfully!', 'success');
                    cart = [];
                    updateCartUI();
                    toggleCartDrawer(false);
                    
                    // Back to dashboard
                    showTab('dashboard');
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showNotification('Submit failed. Please check connectivity.', 'error');
            }
        }


        // ==================== INVENTORY OPERATIONS ====================
        async function loadInventoryProducts(search = '') {
            try {
                const res = await fetch(`api.php?action=get_products&search=${encodeURIComponent(search)}`);
                const data = await res.json();
                
                if (data.success) {
                    productsStore = data.products;
                    renderInventoryList(productsStore);
                }
            } catch (err) {
                console.error(err);
                showNotification('Failed to fetch stock list', 'error');
            }
        }

        function renderInventoryList(list) {
            const listContainer = document.getElementById('inventoryProductsList');
            if (list.length === 0) {
                listContainer.innerHTML = '<div class="text-center py-10 text-xs text-gray-500 font-bold">No products in stock room.</div>';
                return;
            }

            listContainer.innerHTML = list.map(p => {
                const imgHtml = p.image_path 
                    ? `<img src="${p.image_path}" class="w-full h-full object-cover">`
                    : `<span class="font-extrabold text-lime text-xs">${p.name.substring(0,2).toUpperCase()}</span>`;
                const statusBadge = p.status == 1 
                    ? '<span class="text-[9px] font-bold bg-emerald-950/40 text-emerald-400 border border-emerald-500/20 px-2 py-0.5 rounded-md uppercase">Active</span>'
                    : '<span class="text-[9px] font-bold bg-dark-700 text-gray-500 border border-white/5 px-2 py-0.5 rounded-md uppercase">Inactive</span>';
                
                return `
                    <div class="bg-dark-900 border border-white/5 p-4 rounded-3xl space-y-3 relative overflow-hidden">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl bg-dark-800 border border-white/10 flex items-center justify-center overflow-hidden shrink-0">
                                ${imgHtml}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-xs font-bold text-white truncate max-w-[150px]">${p.name}</h3>
                                    ${p.puff ? `<span class="text-[9px] font-bold bg-lime/10 text-lime border border-lime/20 px-2 py-0.5 rounded-md uppercase tracking-wider">${p.puff} Puffs</span>` : ''}
                                    ${statusBadge}
                                </div>
                                <div class="grid grid-cols-3 gap-2 mt-1.5 text-[10px] font-semibold text-gray-400">
                                    <div>
                                        <span class="text-gray-500 block uppercase text-[8px] tracking-wide">Buy</span>
                                        <span class="text-white">Rs. ${parseFloat(p.buying_price).toFixed(0)}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block uppercase text-[8px] tracking-wide">Sell</span>
                                        <span class="text-lime">Rs. ${parseFloat(p.selling_price).toFixed(0)}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 block uppercase text-[8px] tracking-wide">In Stock</span>
                                        <span class="text-white font-bold">${p.stock_quantity} pcs</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex gap-2 border-t border-white/5 pt-3">
                            <button onclick="openProductModalById(${p.id})" class="flex-1 bg-dark-800 border border-white/10 text-white font-bold py-2.5 rounded-xl text-xs tap-scale flex items-center justify-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-lime">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                </svg>
                                <span>Edit</span>
                            </button>
                            <button onclick="deleteProduct(${p.id})" class="bg-red-950/20 border border-red-500/10 text-red-400 font-semibold py-2.5 px-3 rounded-xl text-xs tap-scale flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79" />
                                </svg>
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Live filter Inventory list
        function filterInventoryProducts(query) {
            const searchLower = query.toLowerCase().trim();
            const filtered = productsStore.filter(p => p.name.toLowerCase().includes(searchLower));
            renderInventoryList(filtered);
        }

        // Open modal helpers
        function openProductModal(prod = null) {
            const modal = document.getElementById('productFormModal');
            const form = document.getElementById('productForm');
            const preview = document.getElementById('imagePreviewContainer');
            
            form.reset();
            preview.innerHTML = 'No Image';
            
            if (prod) {
                document.getElementById('productModalTitle').innerText = 'Modify Product';
                document.getElementById('prodId').value = prod.id;
                document.getElementById('prodName').value = prod.name;
                document.getElementById('prodPuff').value = prod.puff || '';
                document.getElementById('prodBuyingPrice').value = parseFloat(prod.buying_price);
                document.getElementById('prodSellingPrice').value = parseFloat(prod.selling_price);
                document.getElementById('prodStock').value = prod.stock_quantity;
                document.getElementById('prodStatus').checked = prod.status == 1;
                
                if (prod.image_path) {
                    preview.innerHTML = `<img src="${prod.image_path}" class="w-full h-full object-cover">`;
                }
            } else {
                document.getElementById('productModalTitle').innerText = 'Add Stock Item';
                document.getElementById('prodId').value = '';
                document.getElementById('prodPuff').value = '';
                document.getElementById('prodStatus').checked = true;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function openProductModalById(id) {
            const prod = productsStore.find(p => p.id == id);
            if (prod) openProductModal(prod);
        }

        function closeProductModal() {
            document.getElementById('productFormModal').classList.add('hidden');
            document.getElementById('productFormModal').classList.remove('flex');
        }

        // Form image preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreviewContainer');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = 'No Image';
            }
        }

        // Save (Add / Edit) Product via AJAX with Upload Progress
        function saveProduct(event) {
            event.preventDefault();
            const form = document.getElementById('productForm');
            const formData = new FormData(form);
            
            // Adjust status checkbox value
            formData.set('status', document.getElementById('prodStatus').checked ? '1' : '0');
            
            const isEdit = formData.get('id') !== '';
            const actionUrl = isEdit ? 'api.php?action=edit_product' : 'api.php?action=add_product';

            // Show progress bar only if a new image file is chosen
            const fileInput = document.getElementById('prodImage');
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            
            const progressWrapper = document.getElementById('uploadProgressWrapper');
            const progressPercent = document.getElementById('uploadProgressPercent');
            const progressBar = document.getElementById('uploadProgressBar');

            if (hasFile) {
                if (progressWrapper) progressWrapper.classList.remove('hidden');
                if (progressPercent) progressPercent.innerText = '0%';
                if (progressBar) progressBar.style.width = '0%';
            }

            const xhr = new XMLHttpRequest();
            xhr.open('POST', actionUrl, true);

            // Setup upload progress listener if file exists
            if (hasFile && xhr.upload) {
                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percent = Math.round((e.loaded / e.total) * 100);
                        if (progressPercent) progressPercent.innerText = percent + '%';
                        if (progressBar) progressBar.style.width = percent + '%';
                    }
                };
            }

            xhr.onload = function() {
                // Reset progress bar on complete
                if (progressWrapper) progressWrapper.classList.add('hidden');
                
                try {
                    const data = JSON.parse(xhr.responseText);
                    if (xhr.status === 200 && data.success) {
                        showNotification(data.message, 'success');
                        closeProductModal();
                        loadInventoryProducts();
                    } else {
                        showNotification(data.message || 'Saving product failed.', 'error');
                    }
                } catch (err) {
                    console.error('XHR response parse error:', xhr.responseText, err);
                    showNotification('Server response error. Please try again.', 'error');
                }
            };

            xhr.onerror = function() {
                if (progressWrapper) progressWrapper.classList.add('hidden');
                showNotification('Submit failed. Connectivity error.', 'error');
            };

            xhr.send(formData);
        }

        // Delete Product via AJAX
        async function deleteProduct(id) {
            if (!confirm('Are you absolutely sure you want to delete this product? All transaction history logs will be retained, but the product item metadata will be purged.')) {
                return;
            }

            try {
                const formData = new FormData();
                formData.append('id', id);

                const res = await fetch('api.php?action=delete_product', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    showNotification(data.message, 'success');
                    loadInventoryProducts();
                } else {
                    showNotification(data.message, 'error');
                }
            } catch (err) {
                console.error(err);
                showNotification('Failed to purge item. Check connection.', 'error');
            }
        }


        // ==================== SALES & REPORTING SYSTEM ====================
        async function loadSales() {
            const start = document.getElementById('filterStartDate').value;
            const end = document.getElementById('filterEndDate').value;
            const partner = document.getElementById('filterPartner').value;

            try {
                const res = await fetch(`api.php?action=get_sales&start_date=${start}&end_date=${end}&user_id=${partner}`);
                const data = await res.json();

                if (data.success) {
                    // Update summary alert
                    document.getElementById('salesFilteredTotal').innerText = `Rs. ${parseFloat(data.summary.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                    document.getElementById('salesFilteredProfit').innerText = `Rs. ${parseFloat(data.summary.total_profit).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
                    
                    // Render Ledger Items list
                    renderSalesList(data.sales);
                    
                    // Render Reports Tables
                    renderDailySummary(data.daily_summary);
                    renderMonthlySummary(data.monthly_summary);
                }
            } catch (err) {
                console.error(err);
                showNotification('Ledger retrieval error.', 'error');
            }
        }

        function renderSalesList(sales) {
            const container = document.getElementById('sales-subtab-list');
            if (sales.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-xs text-gray-500 font-bold">No transacted sales for selected filters.</div>';
                return;
            }

            container.innerHTML = sales.map(s => {
                const date = new Date(s.created_at);
                const fullDateStr = date.toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'}) + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                
                // Construct products bought rows
                const itemsRowsHtml = s.items.map(item => `
                    <div class="flex justify-between items-center py-1 text-[11px] text-gray-400 font-medium">
                        <span>${item.product_name} <span class="text-gray-600">x${item.quantity}</span></span>
                        <span>Rs. ${parseFloat(item.selling_price * item.quantity).toFixed(0)}</span>
                    </div>
                `).join('');

                return `
                    <div class="bg-dark-900 border border-white/5 p-4 rounded-3xl space-y-3 relative overflow-hidden">
                        <div class="flex items-start justify-between border-b border-white/5 pb-2.5">
                            <div>
                                <p class="text-xs font-bold text-white">Sale #00${s.id}</p>
                                <p class="text-[9px] text-gray-500 font-semibold mt-0.5">${fullDateStr} • By ${s.partner_name}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-lime">Rs. ${parseFloat(s.total_amount).toFixed(2)}</p>
                                <p class="text-[9px] text-gray-500 font-bold">Profit: +Rs. ${parseFloat(s.total_profit).toFixed(2)}</p>
                            </div>
                        </div>

                        <!-- Item details -->
                        <div class="space-y-1.5">
                            <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest">Bill Details</p>
                            ${itemsRowsHtml}
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderDailySummary(list) {
            const container = document.getElementById('sales-subtab-daily');
            if (list.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-xs text-gray-500 font-bold">No daily data found.</div>';
                return;
            }

            let rowsHtml = list.map(day => {
                const date = new Date(day.sale_date);
                const format = date.toLocaleDateString([], {month: 'short', day: 'numeric', year: 'numeric'});
                return `
                    <tr class="border-b border-white/5 text-[11px]">
                        <td class="py-3 pl-2 font-bold text-white">${format}</td>
                        <td class="py-3 text-center text-gray-400 font-semibold">${day.txn_count} bills</td>
                        <td class="py-3 text-right font-bold text-white">Rs. ${parseFloat(day.daily_total).toFixed(0)}</td>
                        <td class="py-3 text-right pr-2 font-black text-lime">Rs. ${parseFloat(day.daily_profit).toFixed(0)}</td>
                    </tr>
                `;
            }).join('');

            container.innerHTML = `
                <div class="bg-dark-900 border border-white/5 rounded-3xl overflow-hidden p-2">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/10 text-[9px] text-gray-500 uppercase tracking-wider font-extrabold">
                                <th class="py-2 pl-2">Date</th>
                                <th class="py-2 text-center">Volume</th>
                                <th class="py-2 text-right">Revenue</th>
                                <th class="py-2 text-right pr-2">Net Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function renderMonthlySummary(list) {
            const container = document.getElementById('sales-subtab-monthly');
            if (list.length === 0) {
                container.innerHTML = '<div class="text-center py-10 text-xs text-gray-500 font-bold">No monthly data found.</div>';
                return;
            }

            let rowsHtml = list.map(m => {
                // Split YYYY-MM
                const parts = m.sale_month.split('-');
                const d = new Date(parts[0], parts[1] - 1);
                const label = d.toLocaleDateString([], {month: 'long', year: 'numeric'});
                return `
                    <tr class="border-b border-white/5 text-[11px]">
                        <td class="py-3 pl-2 font-bold text-white">${label}</td>
                        <td class="py-3 text-center text-gray-400 font-semibold">${m.txn_count} bills</td>
                        <td class="py-3 text-right font-bold text-white">Rs. ${parseFloat(m.monthly_total).toFixed(0)}</td>
                        <td class="py-3 text-right pr-2 font-black text-lime">Rs. ${parseFloat(m.monthly_profit).toFixed(0)}</td>
                    </tr>
                `;
            }).join('');

            container.innerHTML = `
                <div class="bg-dark-900 border border-white/5 rounded-3xl overflow-hidden p-2">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-white/10 text-[9px] text-gray-500 uppercase tracking-wider font-extrabold">
                                <th class="py-2 pl-2">Month</th>
                                <th class="py-2 text-center">Volume</th>
                                <th class="py-2 text-right">Revenue</th>
                                <th class="py-2 text-right pr-2">Net Profit</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rowsHtml}
                        </tbody>
                    </table>
                </div>
            `;
        }

        function applySalesFilters() {
            loadSales();
        }

        function switchSalesSubTab(tab) {
            document.querySelectorAll('#tab-sales > div[id^="sales-subtab-"]').forEach(sec => sec.classList.add('hidden'));
            document.querySelectorAll('#tab-sales button[id^="subtab-btn-"]').forEach(btn => {
                btn.classList.remove('border-lime', 'text-lime');
                btn.classList.add('border-transparent', 'text-gray-400');
            });

            document.getElementById(`sales-subtab-${tab}`).classList.remove('hidden');
            const activeBtn = document.getElementById(`subtab-btn-${tab}`);
            activeBtn.classList.remove('border-transparent', 'text-gray-400');
            activeBtn.classList.add('border-lime', 'text-lime');
            
            activeSaleSubTab = tab;
        }


        // ==================== ACCOUNT OPERATIONS ====================
        async function loadAccounts() {
            try {
                const res = await fetch('api.php?action=get_accounts');
                const data = await res.json();

                if (data.success) {
                    renderAccountsList(data.users, data.partnership.profit_split);
                    renderPartnershipCapital(data.partnership);
                }
            } catch (err) {
                console.error(err);
                showNotification('Accounts office failed to refresh.', 'error');
            }
        }

        function renderAccountsList(users, splitProfit) {
            const container = document.getElementById('accountsList');
            
            container.innerHTML = users.map(u => {
                const letter = u.name.charAt(0).toUpperCase();
                const letterBg = u.id == 1 ? 'bg-lime/10 border-lime/30 text-lime' : 'bg-white/10 border-white/20 text-white';
                return `
                    <div class="bg-dark-900 border border-white/5 p-4 rounded-3xl space-y-3.5 relative overflow-hidden">
                        <!-- Header with identity -->
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center font-extrabold text-sm ${letterBg}">
                                ${letter}
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-white">${u.name}</h3>
                                <p class="text-[9px] text-gray-500 font-semibold tracking-wide uppercase">Partner Account</p>
                            </div>
                        </div>

                        <!-- Main ledger columns -->
                        <div class="grid grid-cols-2 gap-3.5 border-t border-white/5 pt-3">
                            <!-- Left: sales/profit indicators -->
                            <div class="space-y-2.5">
                                <div>
                                    <span class="text-[9px] text-gray-500 font-bold block uppercase tracking-wider">Accumulated Sales</span>
                                    <span class="text-xs font-bold text-white">Rs. ${parseFloat(u.total_sales).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] text-gray-500 font-bold block uppercase tracking-wider">Sales Profit Generated</span>
                                    <span class="text-xs font-bold text-white">Rs. ${parseFloat(u.total_profit).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </div>
                            </div>

                            <!-- Right: financial splits -->
                            <div class="space-y-2.5 pl-3 border-l border-white/5 flex flex-col justify-center">
                                <div>
                                    <span class="text-[9px] text-gray-500 font-bold block uppercase tracking-wider">50/50 Profit Split Share</span>
                                    <span class="text-xs font-black text-lime">Rs. ${parseFloat(splitProfit).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Current cash held -->
                        <div class="border-t border-white/5 pt-3 flex justify-between items-center text-xs">
                            <span class="text-gray-500 font-bold uppercase text-[9px] tracking-wider">Direct Cash In Hand Balance</span>
                            <span class="font-extrabold text-lime bg-lime/10 px-3 py-1 rounded-xl">Rs. ${parseFloat(u.cash_balance).toLocaleString('en-US', {minimumFractionDigits: 2})}</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        function renderPartnershipCapital(p) {
            document.getElementById('acctTotalProfit').innerText = `Rs. ${parseFloat(p.total_profit).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('acctTotalCashInHand').innerText = `Rs. ${parseFloat(p.total_cash_in_hand).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            document.getElementById('acctRemainingStockValue').innerText = `Rs. ${parseFloat(p.remaining_stock_value).toLocaleString('en-US', {minimumFractionDigits: 2})}`;
            
            const assetsSum = parseFloat(p.total_cash_in_hand) + parseFloat(p.remaining_stock_value);
            document.getElementById('acctAssetsSum').innerText = `Rs. ${assetsSum.toLocaleString('en-US', {minimumFractionDigits: 2})}`;
        }

        // Service Worker registration for PWA mobile screens
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js')
                    .then(reg => console.log('VX Service Worker registered successfully!'))
                    .catch(err => console.error('VX Service Worker registration failed:', err));
            });
        }

        // Manual PWA install prompt handler
        let deferredPrompt = null;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const btn = document.getElementById('pwaInstallContainer');
            if (btn) btn.classList.remove('hidden');
        });

        async function triggerPwaInstall() {
            if (!deferredPrompt) return;
            deferredPrompt.prompt();
            const { outcome } = await deferredPrompt.userChoice;
            deferredPrompt = null;
            const btn = document.getElementById('pwaInstallContainer');
            if (btn) btn.classList.add('hidden');
        }

        window.addEventListener('appinstalled', () => {
            const btn = document.getElementById('pwaInstallContainer');
            if (btn) btn.classList.add('hidden');
        });

        async function shareCatalog() {
            const shareUrl = window.location.origin + window.location.pathname.replace('index.php', 'catalog.php');
            const shareText = `🔥 Check out our latest products & active price list on the VX Catalog!\n\nBrowse catalog here: ${shareUrl}`;

            try {
                // Try copying to clipboard first
                await navigator.clipboard.writeText(shareText);
                
                // If native share is supported, open it
                if (navigator.share) {
                    await navigator.share({
                        title: 'VX Product Catalog',
                        text: shareText
                    });
                } else {
                    showNotification('Catalog link copied to clipboard!', 'success');
                }
            } catch (err) {
                console.error('Sharing failed:', err);
                showNotification('Link copied to clipboard!', 'success');
            }
        }
    </script>
</body>
</html>
