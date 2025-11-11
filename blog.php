<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// --- Pagination Logic ---
$articles_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $articles_per_page;

// Get total number of articles
$total_articles_result = $conn->query("SELECT COUNT(*) as total FROM articles");
$total_articles = $total_articles_result->fetch_assoc()['total'];
$total_pages = ceil($total_articles / $articles_per_page);

// --- Fetch Articles for the current page ---
$stmt = $conn->prepare("
    SELECT a.*, a.slug, c.name as category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("ii", $articles_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
$articles = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9641979331084216"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog - TCL NEWS</title>
    
    <!-- FAVICON -->
    <link rel="icon" href="img/logo/logo1.png" type="image/png"> 

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    },
                },
            },
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        footer a { text-decoration: none !important; }
        footer a:hover { text-decoration: underline !important; }
    </style>
</head>
<body class="bg-gray-50 font-poppins">

    <header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 ease-in-out bg-white shadow-md text-gray-800">
        <div class="container mx-auto px-4">
            <nav class="py-3 flex justify-between items-center">
                <a href="/" class="text-2xl font-bold flex-shrink-0 flex items-center">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></a>
                <div class="hidden md:flex flex-1 justify-center items-center overflow-hidden mx-4">
                    <div id="desktop-nav-links" class="flex items-center space-x-4 text-sm overflow-x-auto no-scrollbar whitespace-nowrap">
                        <a href="/" class="font-semibold flex-shrink-0">Home</a>
                        <a href="/blog" class="font-semibold flex-shrink-0">Blog</a>
                        <a href="/multimedia" class="font-semibold flex-shrink-0">Multimedia</a>
                        <?php foreach ($all_categories as $category): ?>
                            <a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="transition-colors flex-shrink-0"><?php echo htmlspecialchars($category['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="/search" method="GET" class="hidden md:flex items-center relative">
                        <input type="search" name="query" placeholder="Search..." class="border rounded-full py-1.5 px-4 text-sm focus:outline-none focus:border-blue-500 transition-all w-40">
                        <button type="submit" class="absolute right-3 text-gray-500 hover:text-blue-500"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="/contact" class="font-semibold flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>
                        <span>Contact</span>
                    </a>
                    <button id="mobile-menu-button" class="md:hidden focus:outline-none"><i class="fas fa-bars text-xl"></i></button>
                </div>
            </nav>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200"></div>
    </header>

    <main class="container mx-auto px-4 py-8 pt-24 md:pt-32">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-800 mb-8 border-l-4 border-blue-600 pl-4 font-poppins">All News</h1>
            
            <div class="space-y-8">
                <?php if (!empty($articles)): ?>
                    <?php foreach($articles as $article): ?>
                        <div class="bg-white rounded-lg shadow-md overflow-hidden group flex flex-col md:flex-row">
                            <a href="/article/<?php echo $article['slug']; ?>" class="block md:w-1/3">
                                <img src="/uploads/<?php echo htmlspecialchars($article['image']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x225/e2e8f0/4a5568?text=Image';" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-48 md:h-full object-cover">
                            </a>
                            <div class="p-6 flex-1">
                                <a href="/category/<?php echo urlencode(strtolower($article['category_name'])); ?>" class="text-sm font-semibold text-blue-600 hover:underline"><?php echo htmlspecialchars($article['category_name']); ?></a>
                                <a href="/article/<?php echo $article['slug']; ?>" class="block">
                                   <h2 class="text-xl font-bold text-gray-900 mt-2 group-hover:underline font-poppins"><?php echo htmlspecialchars($article['title']); ?></h2>
                                </a>
                                <p class="text-gray-600 mt-2 text-sm">
                                    <?php 
                                        $content = strip_tags($article['content']);
                                        echo strlen($content) > 150 ? substr($content, 0, 150) . "..." : $content;
                                    ?>
                                </p>
                                <div class="text-xs text-gray-500 mt-4">By <?php echo htmlspecialchars($article['author']); ?> on <?php echo date("F j, Y", strtotime($article['created_at'])); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="bg-white p-8 rounded-lg shadow-md text-center">
                        <p class="text-gray-600">No articles found.</p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="flex justify-center items-center space-x-2 mt-12">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-blue-600 hover:text-white">&laquo; Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="px-4 py-2 <?php echo ($i == $page) ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700'; ?> rounded-md hover:bg-blue-600 hover:text-white"><?php echo $i; ?></a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-blue-600 hover:text-white">Next &raquo;</a>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></h3>
                    <p class="text-gray-400 text-sm mb-4">Your reliable source for the latest news, from technology to entertainment.</p>
                </div>
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">All Categories</h3>
                    <ul class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-6 text-sm">
                        <?php foreach($all_categories as $category): ?>
                           <li><a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="text-white font-semibold hover:underline"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-2 text-sm mb-6">
                        <li><a href="/about" class="text-white font-semibold hover:underline">About Us</a></li>
                        <li><a href="/contact" class="text-white font-semibold hover:underline">Contact</a></li>
                        <li><a href="/privacy" class="text-white font-semibold hover:underline">Privacy Policy</a></li>
                        <li><a href="/terms" class="text-white font-semibold hover:underline">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date("Y"); ?> TCL NEWS. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>