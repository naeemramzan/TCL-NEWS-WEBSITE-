<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// --- Fetch latest 12 articles for the multimedia page ---
$articles_result = $conn->query("
    SELECT a.id, a.title, a.slug, a.image, c.name as category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 12
");
$articles = $articles_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9641979331084216"
     crossorigin="anonymous"></script>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Multimedia - TCL NEWS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com?plugins=aspect-ratio"></script>
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
        .gallery-item .overlay { opacity: 0; transition: opacity 0.3s ease-in-out; }
        .gallery-item:hover .overlay { opacity: 1; }
        .gallery-item .text-content { transform: translateY(20px); transition: transform 0.3s ease-in-out; }
        .gallery-item:hover .text-content { transform: translateY(0); }
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
                        <a href="/blog.php" class="font-semibold flex-shrink-0">Blog</a>
                        <a href="/multimedia.php" class="font-semibold flex-shrink-0">Multimedia</a>
                        <?php foreach ($all_categories as $category): ?>
                            <a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="transition-colors flex-shrink-0"><?php echo htmlspecialchars($category['name']); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <form action="/search.php" method="GET" class="hidden md:flex items-center relative">
                        <input type="search" name="query" placeholder="Search..." class="border rounded-full py-1.5 px-4 text-sm focus:outline-none focus:border-blue-500 transition-all w-40">
                        <button type="submit" class="absolute right-3 text-gray-500 hover:text-blue-500"><i class="fas fa-search"></i></button>
                    </form>
                    <a href="/contact.php" class="font-semibold flex items-center text-blue-600 hover:text-blue-800 transition-colors">
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
        <div class="max-w-6xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-800 mb-8 border-l-4 border-purple-600 pl-4 font-poppins">Multimedia Gallery</h1>
            
            <?php if (!empty($articles)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach($articles as $article): ?>
                        <a href="/article/<?php echo $article['slug']; ?>" class="gallery-item block relative rounded-lg shadow-md overflow-hidden group aspect-w-4 aspect-h-3">
                            <img src="/uploads/<?php echo htmlspecialchars($article['image']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x300/a78bfa/ffffff?text=Media';" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            <div class="overlay absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                            <div class="absolute bottom-0 left-0 p-4 text-white text-content">
                                <span class="text-xs font-semibold uppercase bg-purple-600 px-2 py-1 rounded"><?php echo htmlspecialchars($article['category_name']); ?></span>
                                <h2 class="text-lg font-bold mt-2 font-poppins"><?php echo htmlspecialchars($article['title']); ?></h2>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white p-8 rounded-lg shadow-md text-center">
                    <p class="text-gray-600">No multimedia content found.</p>
                </div>
            <?php endif; ?>
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
                        <li><a href="/about.php" class="text-white font-semibold hover:underline">About Us</a></li>
                        <li><a href="/contact.php" class="text-white font-semibold hover:underline">Contact</a></li>
                        <li><a href="/privacy.php" class="text-white font-semibold hover:underline">Privacy Policy</a></li>
                        <li><a href="/terms.php" class="text-white font-semibold hover:underline">Terms & Conditions</a></li>
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

        const populateMobileMenu = () => {
            if (mobileMenuPopulated || !desktopNavLinks) return;
            mobileMenu.innerHTML = ''; // Clear previous links
            const searchForm = `<div class="p-4 border-b">
                <form action="/search.php" method="GET" class="flex items-center relative">
                    <input type="search" name="query" placeholder="Search articles..." class="w-full border-2 border-gray-300 rounded-full py-2 px-4 text-sm">
                    <button type="submit" class="absolute right-3 text-gray-500"><i class="fas fa-search"></i></button>
                </form>
            </div>`;
            mobileMenu.insertAdjacentHTML('beforeend', searchForm);
            
            const links = desktopNavLinks.querySelectorAll('a');
            links.forEach(link => {
                const mobileLink = link.cloneNode(true);
                mobileLink.className = 'block text-gray-700 py-3 px-4 text-base hover:bg-blue-500 hover:text-white';
                mobileMenu.appendChild(mobileLink);
            });
            mobileMenuPopulated = true;
        };

        if (mobileMenuButton) {
            mobileMenuButton.addEventListener('click', () => {
                if (!mobileMenuPopulated) populateMobileMenu();
                mobileMenu.classList.toggle('hidden');
            });
        }
    });
    </script>
</body>
</html>