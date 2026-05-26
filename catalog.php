<?php
// catalog.php - Publicly sharable product catalog for VX
require_once 'db.php';

try {
    // Fetch all active products
    $stmt = $conn->query("SELECT * FROM `products` WHERE `status` = 1 ORDER BY `name` ASC");
    $products = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en" class="h-full bg-black">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>VX - Product Catalog</title>
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
                        },
                        lime: {
                            DEFAULT: '#ccff00',
                            glow: '#a3e635',
                            dark: '#84cc16',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        /* Modern scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #030303;
        }
        ::-webkit-scrollbar-thumb {
            background: #1c1c1e;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #ccff00;
        }
        
        .neon-glow {
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.08);
        }
        .neon-focus:focus-within {
            box-shadow: 0 0 15px rgba(204, 255, 0, 0.2), 0 0 2px rgba(204, 255, 0, 0.6);
            border-color: #ccff00;
        }
    </style>
</head>
<body class="bg-dark-950 text-gray-200 font-sans selection:bg-lime selection:text-black min-h-screen flex flex-col">

    <!-- Header Section -->
    <header class="sticky top-0 z-40 bg-dark-950/80 backdrop-blur-md border-b border-white/5 py-4 px-4">
        <div class="max-w-4xl mx-auto flex flex-col gap-3.5">
            <!-- Brand Info -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tighter text-white inline-flex items-center gap-0.5">
                        V<span class="text-lime">X</span>
                    </h1>
                    <p class="text-[9px] tracking-[0.25em] text-gray-500 uppercase font-black">Official Catalog</p>
                </div>
                <span class="text-[10px] bg-lime/10 border border-lime/30 text-lime font-extrabold px-3 py-1.5 rounded-full uppercase tracking-wider">
                    <?= count($products) ?> Items Active
                </span>
            </div>

            <!-- Instant Search Input -->
            <div class="relative">
                <div class="flex items-center bg-dark-900 border border-white/10 rounded-2xl p-3.5 neon-focus transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4.5 h-4.5 text-gray-500 mr-2.5 shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                    </svg>
                    <input 
                        type="text" 
                        id="searchInput" 
                        oninput="filterCatalog()" 
                        placeholder="Search items..." 
                        class="bg-transparent border-0 w-full text-white placeholder-gray-600 focus:outline-none focus:ring-0 text-xs font-bold"
                    >
                </div>
            </div>
        </div>
    </header>

    <!-- Main Grid Content -->
    <main class="flex-grow max-w-4xl w-full mx-auto p-4">
        <?php if (empty($products)): ?>
            <div class="flex flex-col items-center justify-center py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-dark-900 border border-white/5 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-7 h-7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 13.5h3.86a2.25 2.25 0 0 1 2.008 1.24l.885 1.77a2.25 2.25 0 0 0 2.007 1.24h1.98a2.25 2.25 0 0 0 2.007-1.24l.885-1.77a2.25 2.25 0 0 1 2.007-1.24h3.86m-18 0h18" />
                    </svg>
                </div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">No Products Available</p>
                <p class="text-[10px] text-gray-600">The catalog is currently empty. Check back later.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-3.5" id="catalogGrid">
                <?php foreach ($products as $p): ?>
                    <div class="product-card bg-dark-900 border border-white/5 p-3 rounded-3xl flex flex-col gap-3 relative overflow-hidden transition-all duration-300 hover:scale-[1.02] hover:border-lime/20 neon-glow" data-name="<?= htmlspecialchars(strtolower($p['name'])) ?>">
                        <!-- Image Container with Aspect Ratio -->
                        <div class="aspect-square w-full rounded-2xl bg-dark-950 border border-white/5 relative overflow-hidden flex items-center justify-center shrink-0">
                            <?php if (!empty($p['image_path'])): ?>
                                <img src="<?= htmlspecialchars($p['image_path']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <!-- Stylized Default Placeholder -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor" class="w-12 h-12 text-white/5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375 0 1 1-.75 0 .375 0 0 1 .75 0Z" />
                                </svg>
                            <?php endif; ?>

                            <!-- Puff Count Badge inside Image -->
                            <?php if (isset($p['puff']) && $p['puff'] !== null && $p['puff'] > 0): ?>
                                <span class="absolute bottom-2.5 left-2.5 bg-lime text-black font-extrabold text-[8px] px-2 py-1 rounded-lg uppercase tracking-wider shadow-md shadow-black/40">
                                    <?= htmlspecialchars($p['puff']) ?> Puffs
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Card Description -->
                        <div class="flex flex-col flex-grow justify-between gap-1.5 px-0.5">
                            <div>
                                <h3 class="text-xs font-bold text-white line-clamp-2 leading-relaxed">
                                    <?= htmlspecialchars($p['name']) ?>
                                </h3>
                            </div>
                            <div class="flex items-center justify-between pt-1 border-t border-white/5 mt-1">
                                <span class="text-[8px] text-gray-500 font-bold uppercase tracking-wider">Price</span>
                                <span class="text-xs font-extrabold text-lime">
                                    Rs. <?= number_format($p['selling_price'], 2) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Empty Search Filter State -->
            <div id="noResults" class="hidden flex flex-col items-center justify-center py-16 text-center space-y-3">
                <div class="w-14 h-14 rounded-2xl bg-dark-900 border border-white/5 flex items-center justify-center text-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.604 10.604Z" />
                    </svg>
                </div>
                <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">No matches found</p>
                <p class="text-[10px] text-gray-600">Try checking spelling or adjusting search filters.</p>
            </div>
        <?php endif; ?>
    </main>

    <!-- Small footer -->
    <footer class="py-6 border-t border-white/5 text-center mt-12 bg-dark-950">
        <p class="text-[9px] text-gray-600 uppercase font-black tracking-widest">© <?= date('Y') ?> VX Partnership • Product Catalog</p>
    </footer>

    <!-- Instant client-side search scripting -->
    <script>
        function filterCatalog() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const cards = document.querySelectorAll('.product-card');
            const grid = document.getElementById('catalogGrid');
            const noResults = document.getElementById('noResults');
            
            let visibleCount = 0;
            
            cards.forEach(card => {
                const name = card.getAttribute('data-name');
                if (name.includes(query)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            if (visibleCount === 0 && query !== "") {
                if (grid) grid.classList.add('hidden');
                if (noResults) noResults.classList.remove('hidden');
            } else {
                if (grid) grid.classList.remove('hidden');
                if (noResults) noResults.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
