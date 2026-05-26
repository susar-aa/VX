<?php
// api.php - Central API controller for AJAX operations
session_start();
header('Content-Type: application/json');

// Security check
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please log in.']);
    exit;
}

require_once 'db.php';

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        
        case 'get_dashboard_stats':
            // Today's Sales
            $today = date('Y-m-d');
            $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) as today_sales FROM sales WHERE DATE(created_at) = :today");
            $stmt->execute(['today' => $today]);
            $todaySales = (float)$stmt->fetchColumn();

            // Total Products (Active)
            $totalProducts = (int)$conn->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn();

            // Total Profit
            $totalProfit = (float)$conn->query("SELECT COALESCE(SUM(total_profit), 0) FROM sales")->fetchColumn();

            // Remaining Stock Value
            $remainingStockValue = (float)$conn->query("SELECT COALESCE(SUM(buying_price * stock_quantity), 0) FROM products WHERE status = 1")->fetchColumn();

            // Logged-in User's cash balance
            $stmt = $conn->prepare("SELECT cash_balance FROM users WHERE id = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $userCashBalance = (float)$stmt->fetchColumn();

            // Partner profit split (50/50)
            $partnerProfitSplit = $totalProfit / 2.0;

            // Recent sales
            $recentSales = $conn->query("
                SELECT s.*, u.name as partner_name 
                FROM sales s 
                JOIN users u ON s.user_id = u.id 
                ORDER BY s.created_at DESC 
                LIMIT 10
            ")->fetchAll();

            $response = [
                'success' => true,
                'stats' => [
                    'today_sales' => $todaySales,
                    'total_products' => $totalProducts,
                    'total_profit' => $totalProfit,
                    'remaining_stock_value' => $remainingStockValue,
                    'user_cash_balance' => $userCashBalance,
                    'partner_profit_split' => $partnerProfitSplit
                ],
                'recent_sales' => $recentSales
            ];
            break;

        case 'get_products':
            $search = trim($_GET['search'] ?? '');
            if ($search !== '') {
                $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE :search ORDER BY name ASC");
                $stmt->execute(['search' => "%$search%"]);
            } else {
                $stmt = $conn->query("SELECT * FROM products ORDER BY name ASC");
            }
            $products = $stmt->fetchAll();
            $response = ['success' => true, 'products' => $products];
            break;

        case 'add_product':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $name = trim($_POST['name'] ?? '');
            $buying_price = (float)($_POST['buying_price'] ?? 0);
            $selling_price = (float)($_POST['selling_price'] ?? 0);
            $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
            $status = (int)($_POST['status'] ?? 1);
            $puff = isset($_POST['puff']) && $_POST['puff'] !== '' ? (int)$_POST['puff'] : null;

            if (empty($name)) {
                throw new Exception('Product name is required');
            }

            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $uploadError = $_FILES['image']['error'];
                    $errMap = [
                        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
                    ];
                    $errMsg = $errMap[$uploadError] ?? 'Unknown upload error (code: ' . $uploadError . ').';
                    throw new Exception('Image upload failed: ' . $errMsg);
                }

                // Ensure upload directory exists
                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }
                
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $newFileName = uniqid('prod_', true) . ($fileExtension !== '' ? '.' . $fileExtension : '');
                $destPath = 'uploads/' . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $imagePath = $destPath;
                } else {
                    throw new Exception('Failed to save uploaded image');
                }
            }

            $stmt = $conn->prepare("INSERT INTO products (name, puff, buying_price, selling_price, stock_quantity, image_path, status) VALUES (:name, :puff, :buying_price, :selling_price, :stock_quantity, :image_path, :status)");
            $stmt->execute([
                'name' => $name,
                'puff' => $puff,
                'buying_price' => $buying_price,
                'selling_price' => $selling_price,
                'stock_quantity' => $stock_quantity,
                'image_path' => $imagePath,
                'status' => $status
            ]);

            $response = ['success' => true, 'message' => 'Product added successfully!'];
            break;

        case 'edit_product':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $buying_price = (float)($_POST['buying_price'] ?? 0);
            $selling_price = (float)($_POST['selling_price'] ?? 0);
            $stock_quantity = (int)($_POST['stock_quantity'] ?? 0);
            $status = (int)($_POST['status'] ?? 1);
            $puff = isset($_POST['puff']) && $_POST['puff'] !== '' ? (int)$_POST['puff'] : null;

            if ($id <= 0 || empty($name)) {
                throw new Exception('Valid product ID and name are required');
            }

            // Get existing product to preserve image or delete old if new uploaded
            $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $existingProduct = $stmt->fetch();
            if (!$existingProduct) {
                throw new Exception('Product not found');
            }

            $imagePath = $existingProduct['image_path'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                    $uploadError = $_FILES['image']['error'];
                    $errMap = [
                        UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                        UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                        UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                        UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                        UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                        UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
                    ];
                    $errMsg = $errMap[$uploadError] ?? 'Unknown upload error (code: ' . $uploadError . ').';
                    throw new Exception('Image upload failed: ' . $errMsg);
                }

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0755, true);
                }
                
                $fileTmpPath = $_FILES['image']['tmp_name'];
                $fileName = $_FILES['image']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                
                $newFileName = uniqid('prod_', true) . ($fileExtension !== '' ? '.' . $fileExtension : '');
                $destPath = 'uploads/' . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    // Delete old image if exists
                    if ($imagePath && file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                    $imagePath = $destPath;
                } else {
                    throw new Exception('Failed to save uploaded image');
                }
            }

            $stmt = $conn->prepare("UPDATE products SET name = :name, puff = :puff, buying_price = :buying_price, selling_price = :selling_price, stock_quantity = :stock_quantity, image_path = :image_path, status = :status WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'puff' => $puff,
                'buying_price' => $buying_price,
                'selling_price' => $selling_price,
                'stock_quantity' => $stock_quantity,
                'image_path' => $imagePath,
                'status' => $status,
                'id' => $id
            ]);

            $response = ['success' => true, 'message' => 'Product updated successfully!'];
            break;

        case 'delete_product':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid product ID');
            }

            // Get image to delete
            $stmt = $conn->prepare("SELECT image_path FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $imagePath = $stmt->fetchColumn();

            // Hard delete product (Cascades to sale_items if any due to DB foreign keys)
            $stmt = $conn->prepare("DELETE FROM products WHERE id = :id");
            $stmt->execute(['id' => $id]);

            if ($imagePath && file_exists($imagePath)) {
                @unlink($imagePath);
            }

            $response = ['success' => true, 'message' => 'Product deleted successfully!'];
            break;

        case 'complete_sale':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $cartJson = $_POST['cart'] ?? '[]';
            $cart = json_decode($cartJson, true);

            if (empty($cart)) {
                throw new Exception('Your cart is empty.');
            }

            $conn->beginTransaction();

            $totalSaleAmount = 0.0;
            $totalSaleProfit = 0.0;
            $saleItemsToInsert = [];

            // 1. Process and validate each cart item
            foreach ($cart as $item) {
                $productId = (int)($item['id'] ?? 0);
                $qty = (int)($item['quantity'] ?? 0);
                $customSellingPrice = (float)($item['selling_price'] ?? 0);

                if ($productId <= 0 || $qty <= 0) {
                    throw new Exception('Invalid cart item data.');
                }

                // Get fresh stock and pricing from DB
                $stmt = $conn->prepare("SELECT * FROM products WHERE id = :id FOR UPDATE");
                $stmt->execute(['id' => $productId]);
                $product = $stmt->fetch();

                if (!$product) {
                    throw new Exception("Product ID $productId not found.");
                }

                if ($product['status'] != 1) {
                    throw new Exception("Product '" . $product['name'] . "' is currently inactive.");
                }

                if ($product['stock_quantity'] < $qty) {
                    throw new Exception("Insufficient stock for '" . $product['name'] . "'. Available: " . $product['stock_quantity']);
                }

                $buyingPrice = (float)$product['buying_price'];
                $itemProfit = ($customSellingPrice - $buyingPrice) * $qty;

                $totalSaleAmount += ($customSellingPrice * $qty);
                $totalSaleProfit += $itemProfit;

                $saleItemsToInsert[] = [
                    'product_id' => $productId,
                    'quantity' => $qty,
                    'buying_price' => $buyingPrice,
                    'selling_price' => $customSellingPrice,
                    'new_stock' => $product['stock_quantity'] - $qty
                ];
            }

            // 2. Insert Sale record
            $userId = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO sales (user_id, total_amount, total_profit) VALUES (:user_id, :total_amount, :total_profit)");
            $stmt->execute([
                'user_id' => $userId,
                'total_amount' => $totalSaleAmount,
                'total_profit' => $totalSaleProfit
            ]);
            $saleId = $conn->lastInsertId();

            // 3. Insert Sale Items and update stocks
            $insertItemStmt = $conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, buying_price, selling_price) VALUES (:sale_id, :product_id, :quantity, :buying_price, :selling_price)");
            $updateStockStmt = $conn->prepare("UPDATE products SET stock_quantity = :new_stock WHERE id = :product_id");

            foreach ($saleItemsToInsert as $item) {
                $insertItemStmt->execute([
                    'sale_id' => $saleId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'buying_price' => $item['buying_price'],
                    'selling_price' => $item['selling_price']
                ]);

                $updateStockStmt->execute([
                    'new_stock' => $item['new_stock'],
                    'product_id' => $item['product_id']
                ]);
            }

            // 4. Assign entire sale amount to the logged-in user's cash balance
            $stmt = $conn->prepare("UPDATE users SET cash_balance = cash_balance + :sale_amount WHERE id = :user_id");
            $stmt->execute([
                'sale_amount' => $totalSaleAmount,
                'user_id' => $userId
            ]);

            $conn->commit();
            $response = ['success' => true, 'message' => 'Sale completed successfully!', 'sale_id' => $saleId];
            break;

        case 'get_sales':
            // Date Filter
            $startDate = $_GET['start_date'] ?? '';
            $endDate = $_GET['end_date'] ?? '';
            // Partner Filter
            $partnerId = $_GET['user_id'] ?? '';

            $whereClauses = [];
            $params = [];

            if (!empty($startDate)) {
                $whereClauses[] = "DATE(s.created_at) >= :start_date";
                $params['start_date'] = $startDate;
            }
            if (!empty($endDate)) {
                $whereClauses[] = "DATE(s.created_at) <= :end_date";
                $params['end_date'] = $endDate;
            }
            if (!empty($partnerId)) {
                $whereClauses[] = "s.user_id = :user_id";
                $params['user_id'] = (int)$partnerId;
            }

            $whereSql = '';
            if (!empty($whereClauses)) {
                $whereSql = "WHERE " . implode(" AND ", $whereClauses);
            }

            // Retrieve matching sales
            $query = "
                SELECT s.*, u.name as partner_name 
                FROM sales s 
                JOIN users u ON s.user_id = u.id 
                $whereSql 
                ORDER BY s.created_at DESC
            ";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $sales = $stmt->fetchAll();

            // Calculate summaries for the filtered set
            $filteredTotalAmount = 0.0;
            $filteredTotalProfit = 0.0;
            foreach ($sales as &$sale) {
                $filteredTotalAmount += (float)$sale['total_amount'];
                $filteredTotalProfit += (float)$sale['total_profit'];
                
                // Fetch items for each sale to display details
                $itemsStmt = $conn->prepare("
                    SELECT si.*, p.name as product_name, p.image_path
                    FROM sale_items si
                    JOIN products p ON si.product_id = p.id
                    WHERE si.sale_id = :sale_id
                ");
                $itemsStmt->execute(['sale_id' => $sale['id']]);
                $sale['items'] = $itemsStmt->fetchAll();
            }

            // Daily sales summary for reports (grouped by date)
            $dailyQuery = "
                SELECT DATE(s.created_at) as sale_date, SUM(s.total_amount) as daily_total, SUM(s.total_profit) as daily_profit, COUNT(*) as txn_count
                FROM sales s
                $whereSql
                GROUP BY DATE(s.created_at)
                ORDER BY sale_date DESC
                LIMIT 30
            ";
            $dailyStmt = $conn->prepare($dailyQuery);
            $dailyStmt->execute($params);
            $dailySummary = $dailyStmt->fetchAll();

            // Monthly sales summary for reports (grouped by month)
            $monthlyQuery = "
                SELECT DATE_FORMAT(s.created_at, '%Y-%m') as sale_month, SUM(s.total_amount) as monthly_total, SUM(s.total_profit) as monthly_profit, COUNT(*) as txn_count
                FROM sales s
                $whereSql
                GROUP BY DATE_FORMAT(s.created_at, '%Y-%m')
                ORDER BY sale_month DESC
            ";
            $monthlyStmt = $conn->prepare($monthlyQuery);
            $monthlyStmt->execute($params);
            $monthlySummary = $monthlyStmt->fetchAll();

            $response = [
                'success' => true,
                'sales' => $sales,
                'summary' => [
                    'total_amount' => $filteredTotalAmount,
                    'total_profit' => $filteredTotalProfit
                ],
                'daily_summary' => $dailySummary,
                'monthly_summary' => $monthlySummary
            ];
            break;

        case 'get_accounts':
            // Retrieve both users with their cash balances and calculate metrics
            $users = $conn->query("SELECT id, name, username, cash_balance FROM users ORDER BY name ASC")->fetchAll();
            
            // For each user, fetch their generated sales & profit
            foreach ($users as &$u) {
                // Generated Sales
                $stmt = $conn->prepare("SELECT COALESCE(SUM(total_amount), 0) FROM sales WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $u['id']]);
                $u['total_sales'] = (float)$stmt->fetchColumn();

                // Generated Profit
                $stmt = $conn->prepare("SELECT COALESCE(SUM(total_profit), 0) FROM sales WHERE user_id = :user_id");
                $stmt->execute(['user_id' => $u['id']]);
                $u['total_profit'] = (float)$stmt->fetchColumn();
            }

            // System-wide calculations
            $totalProfit = (float)$conn->query("SELECT COALESCE(SUM(total_profit), 0) FROM sales")->fetchColumn();
            $totalCashInHand = (float)$conn->query("SELECT COALESCE(SUM(cash_balance), 0) FROM users")->fetchColumn();
            $remainingStockValue = (float)$conn->query("SELECT COALESCE(SUM(buying_price * stock_quantity), 0) FROM products WHERE status = 1")->fetchColumn();

            // Profit split: 50/50
            $profitSplit = $totalProfit / 2.0;

            $response = [
                'success' => true,
                'users' => $users,
                'partnership' => [
                    'total_profit' => $totalProfit,
                    'profit_split' => $profitSplit,
                    'total_cash_in_hand' => $totalCashInHand,
                    'remaining_stock_value' => $remainingStockValue
                ]
            ];
            break;

        default:
            throw new Exception("Unknown action: $action");
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];
}

echo json_encode($response);
?>
