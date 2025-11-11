<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// --- Initialize variables ---
$category_name = 'Category';
$category_hero_slides = [];
$articles = [];
$category_exists = false;

// --- Get Category Details and Articles ---
if (isset($_GET['name'])) {
    $category_slug = $_GET['name'];
    
    // Check if category exists and get its proper name
    $stmt_cat = $conn->prepare("SELECT name FROM categories WHERE name = ?");
    $stmt_cat->bind_param("s", $category_slug);
    $stmt_cat->execute();
    $cat_result = $stmt_cat->get_result();
    if($cat_result->num_rows > 0) {
        $category_exists = true;
        $category_data = $cat_result->fetch_assoc();
        $category_name = $category_data['name'];
    }
    $stmt_cat->close();

    if ($category_exists) {
        // --- Fetch 5 latest articles for the NEW Category Hero Slider ---
        $stmt_hero = $conn->prepare("
            SELECT a.id, a.title, a.slug, a.image FROM articles a
            JOIN categories c ON a.category_id = c.id
            WHERE c.name = ? ORDER BY a.created_at DESC LIMIT 5
        ");
        $stmt_hero->bind_param("s", $category_slug);
        $stmt_hero->execute();
        $hero_result = $stmt_hero->get_result();
        $category_hero_slides = $hero_result->fetch_all(MYSQLI_ASSOC);
        $stmt_hero->close();

        // --- Fetch ALL articles for the main grid ---
        $stmt_all = $conn->prepare("
            SELECT a.*, a.slug FROM articles a
            JOIN categories c ON a.category_id = c.id
            WHERE c.name = ? ORDER BY a.created_at DESC
        ");
        $stmt_all->bind_param("s", $category_slug);
        $stmt_all->execute();
        $articles_result = $stmt_all->get_result();
        $articles = $articles_result->fetch_all(MYSQLI_ASSOC);
        $stmt_all->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3637721699586342"
     crossorigin="anonymous"></script>
     
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(ucfirst($category_name)); ?> - TCL NEWS</title>

    <!-- FAVICON -->
    <link rel="icon" href="img/logo/logo1.png" type="image/png"> 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com?plugins=aspect-ratio"></script>
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'], serif: ['Playfair Display', 'serif'] } } } }
    </script>
     <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.6); }
        footer a {
            text-decoration: none !important;
        }
        footer a:hover {
            text-decoration: underline !important;
        }
     </style>
</head>
<body class="bg-gray-50 font-poppins">

    <header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 ease-in-out bg-white shadow-md text-gray-800">
        <div class="container mx-auto px-4">
            <nav class="py-3 flex justify-between items-center">
                <a href="/index.php" class="text-2xl font-bold flex-shrink-0 flex items-center">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></a>
                <div class="hidden md:flex flex-1 justify-center items-center overflow-hidden mx-4">
                    <div id="desktop-nav-links" class="flex items-center space-x-4 text-sm overflow-x-auto no-scrollbar whitespace-nowrap">
                        <a href="/index.php" class="font-semibold flex-shrink-0">Home</a>
                        <?php foreach ($all_categories as $category): ?>
                            <a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="transition-colors flex-shrink-0 <?php echo ($category['name'] == $category_name) ? 'font-bold' : ''; ?>"><?php echo htmlspecialchars($category['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="/search.php" method="GET" class="hidden md:flex items-center relative">
                        <input type="search" name="query" placeholder="Search..." class="border rounded-full py-1.5 px-4 text-sm focus:outline-none focus:border-blue-500 transition-all w-40">
                        <button type="submit" class="absolute right-3 text-gray-500 hover:text-blue-500"><i class="fas fa-search"></i></button>
                    </form>
                    <button id="mobile-menu-button" class="md:hidden focus:outline-none"><i class="fas fa-bars text-xl"></i></button>
                </div>
            </nav>
        </div>
        <div id="mobile-menu" class="hidden md:hidden bg-white/95 backdrop-blur-sm border-t border-gray-200"></div>
    </header>
    
    <?php if (!empty($category_hero_slides)): ?>
    <section id="hero-slider" class="relative w-full overflow-hidden bg-gray-900 h-80 md:h-[28rem] lg:h-[32rem] mt-16">
        <?php foreach ($category_hero_slides as $index => $slide): ?>
            <a href="/article/<?php echo $slide['slug']; ?>" class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out <?php echo $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                <img src="/uploads/<?php echo htmlspecialchars($slide['image']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent"></div>
                <div class="absolute bottom-0 left-0 p-8 md:p-16 text-white max-w-4xl z-20">
                    <span class="inline-block bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded mb-3">Latest in <?php echo htmlspecialchars(ucfirst($category_name)); ?></span>
                    <h1 class="text-3xl md:text-5xl font-bold font-serif leading-tight text-shadow"><?php echo htmlspecialchars($slide['title']); ?></h1>
                </div>
            </a>
        <?php endforeach; ?>
         <div id="slider-dots" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-30">
            <?php foreach ($category_hero_slides as $index => $slide): ?>
                <button class="slider-dot h-3 w-3 rounded-full transition-colors duration-300 <?php echo $index === 0 ? 'bg-white' : 'bg-white/50'; ?>" data-slide-to="<?php echo $index; ?>"></button>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>


    <main class="container mx-auto px-4 py-12">
        
        <?php if (empty($category_hero_slides) && $category_exists): ?>
            <div class="pt-24 text-center"> <h1 class="text-5xl font-bold font-serif text-gray-800"><?php echo htmlspecialchars(ucfirst($category_name)); ?></h1>
                <p class="text-gray-500 mt-2">A collection of articles from the '<?php echo htmlspecialchars(ucfirst($category_name)); ?>' category.</p>
            </div>
        <?php endif; ?>

        <?php if (!empty($category_hero_slides)): ?>
             <h2 class="text-3xl font-bold text-gray-800 mb-8 border-l-4 border-blue-600 pl-4 font-serif">More in <?php echo htmlspecialchars(ucfirst($category_name)); ?></h2>
        <?php endif; ?>

        <?php if ($category_exists && !empty($articles)): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($articles as $article): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <a href="/article/<?php echo $article['slug']; ?>" class="block">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="/uploads/<?php echo htmlspecialchars($article['image']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x225/e2e8f0/4a5568?text=Image';" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 leading-tight group-hover:text-blue-600 transition-colors"><?php echo htmlspecialchars($article['title']); ?></h3>
                                <div class="text-xs text-gray-500 mt-2">By <?php echo htmlspecialchars($article['author']); ?></div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-20">
                <h2 class="text-2xl font-semibold text-gray-700">Category Not Found or Empty</h2>
                <p class="text-gray-500 mt-2">The category you're looking for doesn't exist or has no articles yet.</p>
                <a href="/index.php" class="mt-6 inline-block bg-blue-600 text-white font-semibold px-5 py-2 rounded-lg hover:bg-blue-700">Back to Homepage</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></h3>
                    <p class="text-gray-400 text-sm mb-4">Your reliable source for the latest news.</p>
                </div>
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">All Categories</h3>
                    <ul id="footer-categories" class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-6 text-sm">
                        <?php foreach($all_categories as $category): ?>
                           <li><a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="text-gray-400 hover:text-white"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul id="footer-quick-links" class="space-y-2 text-sm">
                        <li><a href="/about.php" class="text-gray-400 hover:text-white">About Us</a></li>
                        <li><a href="/contact.php" class="text-gray-400 hover:text-white">Contact</a></li>
                        <li><a href="/privacy.php" class="text-gray-400 hover:text-white">Privacy Policy</a></li>
                        <li><a href="/terms.php" class="text-gray-400 hover:text-white">Terms & Conditions</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-700 mt-8 pt-6 text-center text-sm text-gray-500">
                <p>&copy; <?php echo date("Y"); ?> TCL NEWS. All Rights Reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const desktopNavLinks = document.getElementById('desktop-nav-links');
        let mobileMenuPopulated = false;

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => {
                if (!mobileMenuPopulated) {
                    const links = desktopNavLinks.querySelectorAll('a');
                    links.forEach(link => {
                        const mobileLink = link.cloneNode(true);
                        mobileLink.className = 'block text-gray-700 py-3 px-4 text-base hover:bg-blue-500 hover:text-white';
                        mobileMenu.appendChild(mobileLink);
                    });
                    mobileMenuPopulated = true;
                }
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
    </script>
</body>
</html>