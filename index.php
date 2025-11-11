<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// --- Fetch 5 Articles for the NEW Hero Slider ---
$hero_slider_result = $conn->query("
    SELECT a.id, a.title, a.slug, a.image, c.name AS category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 5
");
$hero_slides = $hero_slider_result->fetch_all(MYSQLI_ASSOC);

// --- Fetch Original Hero Section Articles (Top 3 latest) ---
$hero_articles_result = $conn->query("
    SELECT a.*, a.slug, c.name AS category_name
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    ORDER BY a.created_at DESC
    LIMIT 3
");

// Helper function to fetch articles for a specific category section
function get_articles_by_category($conn, $category_name, $limit = 4) {
    $stmt = $conn->prepare("
        SELECT a.*, a.slug FROM articles a
        JOIN categories c ON a.category_id = c.id
        WHERE c.name = ?
        ORDER BY a.created_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("si", $category_name, $limit);
    $stmt->execute();
    return $stmt->get_result();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9641979331084216"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TCL NEWS - Your Daily Dose of News</title>

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
            plugins: [
                require('@tailwindcss/aspect-ratio'),
            ],
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        .text-shadow { text-shadow: 0 2px 4px rgba(0,0,0,0.5); }
        footer a { text-decoration: none !important; }
        footer a:hover { text-decoration: underline !important; }
    </style>
</head>
<body class="bg-gray-50 font-poppins">

    <header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 ease-in-out text-white">
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
                        <input type="search" name="query" placeholder="Search..." class="border rounded-full py-1.5 px-4 text-sm focus:outline-none focus:border-blue-500 transition-all w-40 bg-white/30 placeholder-white/70 focus:bg-white focus:placeholder-gray-500">
                        <button type="submit" class="absolute right-3 text-white/70 hover:text-blue-500"><i class="fas fa-search"></i></button>
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

    <section id="hero-slider" class="relative w-full overflow-hidden bg-gray-900 h-96 lg:h-[36rem]">
        <?php foreach ($hero_slides as $index => $slide): ?>
            <div class="hero-slide absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out <?php echo $index === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0'; ?>">
                <img src="/uploads/<?php echo htmlspecialchars($slide['image']); ?>" class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                <div class="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent"></div>
                <a href="/article/<?php echo $slide['slug']; ?>" class="absolute inset-0 z-20">
                    <div class="absolute bottom-0 left-0 p-8 md:p-16 text-white max-w-4xl">
                        <span class="inline-block bg-blue-600 text-white text-sm font-semibold px-3 py-1 rounded mb-3"><?php echo htmlspecialchars($slide['category_name']); ?></span>
                        <h1 class="text-3xl md:text-5xl font-bold font-poppins leading-tight text-shadow"><?php echo htmlspecialchars($slide['title']); ?></h1>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
        <div id="slider-dots" class="absolute bottom-6 left-1/2 -translate-x-1/2 flex space-x-3 z-30">
            <?php foreach ($hero_slides as $index => $slide): ?>
                <button class="slider-dot h-3 w-3 rounded-full transition-colors duration-300 <?php echo $index === 0 ? 'bg-white' : 'bg-white/50'; ?>" data-slide-to="<?php echo $index; ?>"></button>
            <?php endforeach; ?>
        </div>
    </section>

    <main class="container mx-auto px-4 py-8">
        <section id="top-stories" class="mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-6 border-l-4 border-blue-600 pl-4 font-poppins">Top Stories</h2>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <?php
                $hero_articles = $hero_articles_result->fetch_all(MYSQLI_ASSOC);
                if (!empty($hero_articles)):
                    $main_article = array_shift($hero_articles);
                ?>
                <div class="lg:col-span-2 bg-white rounded-lg shadow-md overflow-hidden group">
                    <a href="/article/<?php echo $main_article['slug']; ?>" class="block">
                        <div class="aspect-w-16 aspect-h-9">
                            <img src="/uploads/<?php echo htmlspecialchars($main_article['image']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/800x450/3b82f6/ffffff?text=Image+Not+Found';" alt="<?php echo htmlspecialchars($main_article['title']); ?>" class="w-full h-full object-cover">
                        </div>
                        <div class="p-6">
                           <span class="text-sm font-semibold text-red-600"><?php echo htmlspecialchars($main_article['category_name']); ?></span>
                           <h3 class="text-2xl font-bold text-gray-900 mt-2 group-hover:underline font-poppins"><?php echo htmlspecialchars($main_article['title']); ?></h3>
                        </div>
                    </a>
                </div>
                <div class="space-y-6">
                    <?php foreach($hero_articles as $index => $side_article): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                        <a href="/article/<?php echo $side_article['slug']; ?>" class="flex flex-col h-full">
                            <div class="aspect-w-16 aspect-h-9">
                                <img src="/uploads/<?php echo htmlspecialchars($side_article['image']); ?>" onerror="this.onerror=null;this.src='https://placehold.co/400x225/e2e8f0/4a5568?text=Image';" alt="<?php echo htmlspecialchars($side_article['title']); ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-4">
                                <span class="text-sm font-semibold text-blue-600"><?php echo htmlspecialchars($side_article['category_name']); ?></span>
                                <h4 class="text-md font-bold text-gray-800 mt-1 group-hover:underline font-poppins"><?php echo htmlspecialchars($side_article['title']); ?></h4>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <p class="text-gray-500 lg:col-span-3">No top stories found.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php
            $colors = ['blue', 'red', 'green', 'purple', 'yellow', 'indigo', 'pink', 'teal'];
            $color_index = 0;
            foreach ($all_categories as $category) {
                $category_name = $category['name'];
                $articles_result = get_articles_by_category($conn, $category_name);
                if ($articles_result->num_rows > 0) {
                    $color = $colors[$color_index % count($colors)];
                    echo '
                    <section class="mb-12">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-gray-800 border-l-4 border-' . $color . '-600 pl-4">' . htmlspecialchars($category_name) . '</h2>
                            <a href="/category/' . urlencode(strtolower($category_name)) . '" class="text-sm font-semibold text-' . $color . '-600 hover:underline">View All &rarr;</a>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">';
                    while ($article = $articles_result->fetch_assoc()) {
                        echo '
                            <div class="bg-white rounded-lg shadow-md overflow-hidden group">
                                <a href="/article/'. $article['slug'] .'" class="block">
                                    <div class="aspect-w-16 aspect-h-9">
                                        <img src="/uploads/' . htmlspecialchars($article['image']) . '" onerror="this.onerror=null;this.src=\'https://placehold.co/400x225/e2e8f0/4a5568?text=Image\';" alt="' . htmlspecialchars($article['title']) . '" class="w-full h-full object-cover">
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-800 h-auto group-hover:underline font-poppins">' . htmlspecialchars($article['title']) . '</h3>
                                        <div class="text-xs text-gray-500 mt-2">By ' . htmlspecialchars($article['author']) . '</div>
                                    </div>
                                </a>
                            </div>';
                    }
                    echo '</div></section>';
                    $color_index++;
                }
            }
        ?>
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></h3>
                    <p class="text-gray-400 text-sm mb-4">Your reliable source for the latest news, from technology to entertainment.</p>
                </div>
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">Content Sections</h3>
                    <ul id="footer-categories" class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-6 text-sm">
                        <li><a href="/blog" class="text-white font-semibold hover:underline">Blog</a></li>
                        <li><a href="/multimedia" class="text-white font-semibold hover:underline">Multimedia</a></li>
                        <?php foreach($all_categories as $category): ?>
                           <li><a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="text-white font-semibold hover:underline"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul id="footer-quick-links" class="space-y-2 text-sm mb-6">
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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const desktopNavLinks = document.getElementById('desktop-nav-links');
        let mobileMenuPopulated = false;

        const populateMobileMenu = () => {
            if (mobileMenuPopulated || !desktopNavLinks) return;
            mobileMenu.innerHTML = '';
            const searchForm = `<div class="p-4 border-b">
                <form action="/search" method="GET" class="flex items-center relative">
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
        
        // --- Hero Slider Logic ---
        const slides = document.querySelectorAll('.hero-slide');
        const dots = document.querySelectorAll('.slider-dot');
        if (slides.length > 1) {
            let currentSlide = 0;
            let slideInterval = setInterval(() => showSlide((currentSlide + 1) % slides.length), 5000);
            const showSlide = (n) => {
                slides.forEach((s, i) => {
                    s.style.opacity = (i === n) ? '1' : '0';
                    s.style.zIndex = (i === n) ? '10' : '0';
                });
                dots.forEach((d, i) => d.classList.toggle('bg-white', i === n));
                currentSlide = n;
            };
            dots.forEach((dot, i) => dot.addEventListener('click', () => {
                showSlide(i);
                clearInterval(slideInterval);
                slideInterval = setInterval(() => showSlide((currentSlide + 1) % slides.length), 5000);
            }));
        }

        // --- NEW: Floating Header Logic ---
        const header = document.getElementById('main-header');
        const searchInput = header.querySelector('input[type="search"]');

        const updateHeaderStyle = () => {
            if (window.scrollY > 50) {
                header.classList.add('bg-white', 'shadow-md', 'text-gray-800');
                header.classList.remove('text-white');
                 if(searchInput) {
                    searchInput.classList.remove('bg-white/30', 'placeholder-white/70');
                    searchInput.classList.add('focus:placeholder-gray-500', 'text-gray-900');
                 }
            } else {
                header.classList.remove('bg-white', 'shadow-md', 'text-gray-800');
                header.classList.add('text-white');
                if(searchInput) {
                    searchInput.classList.add('bg-white/30', 'placeholder-white/70');
                    searchInput.classList.remove('focus:placeholder-gray-500', 'text-gray-900');
                }
            }
        };
        window.addEventListener('scroll', updateHeaderStyle);
        updateHeaderStyle(); // Run on page load

    });
    </script>
</body>
</html>