<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);

// --- Fetch the main article based on SLUG ---
$article = null;
$related_articles = [];
if (isset($_GET['slug'])) {
    $article_slug = $_GET['slug'];

    // Prepare statement to fetch article by slug
    $stmt = $conn->prepare("
        SELECT a.*, c.name AS category_name
        FROM articles a
        JOIN categories c ON a.category_id = c.id
        WHERE a.slug = ?
    ");
    $stmt->bind_param("s", $article_slug);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $article = $result->fetch_assoc();
        $article_id = $article['id'];
        
        // --- Fetch related articles from the same category ---
        $category_id = $article['category_id'];
        $stmt_related = $conn->prepare("
            SELECT id, title, image, slug FROM articles
            WHERE category_id = ? AND id != ?
            ORDER BY created_at DESC
            LIMIT 4
        ");
        $stmt_related->bind_param("ii", $category_id, $article_id);
        $stmt_related->execute();
        $related_result = $stmt_related->get_result();
        $related_articles = $related_result->fetch_all(MYSQLI_ASSOC);
        $stmt_related->close();
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9641979331084216"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $article ? htmlspecialchars($article['title']) : 'Article Not Found'; ?> - TCL NEWS</title>
    
    <!-- FAVICON -->
    <link rel="icon" href="img/logo/logo1.png" type="image/png"> 
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@700&family=Source+Serif+4:opsz,wght@8..60,400;8..60,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://cdn.tailwindcss.com?plugins=typography,aspect-ratio"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                        'source-serif': ['Source Serif 4', 'serif'],
                    }
                },
            }
        }
    </script>
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        footer a { text-decoration: none !important; }
        footer a:hover { text-decoration: underline !important; }
    </style>
</head>
<body class="bg-white font-poppins">
    
    <header id="main-header" class="fixed top-0 w-full z-50 transition-all duration-300 ease-in-out bg-white shadow-md text-gray-800">
        <div class="container mx-auto px-4">
            <nav class="py-3 flex justify-between items-center">
                <a href="/index.php" class="text-2xl font-bold flex-shrink-0 flex items-center">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></a>
                 <div class="hidden md:flex flex-1 justify-center items-center overflow-hidden mx-4">
                    <div id="desktop-nav-links" class="flex items-center space-x-4 text-sm overflow-x-auto no-scrollbar whitespace-nowrap">
                        <a href="/index.php" class="font-semibold flex-shrink-0">Home</a>
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

    <main class="container mx-auto px-4 py-8 pt-24">
        <?php if ($article): ?>
            <div class="max-w-6xl mx-auto">
                <div class="mb-8 text-center">
                    <a href="/category/<?php echo urlencode(strtolower($article['category_name'])); ?>" class="text-sm font-bold uppercase text-blue-600 hover:underline"><?php echo htmlspecialchars($article['category_name']); ?></a>
                    <h1 class="text-4xl md:text-6xl font-bold font-poppins text-gray-900 mt-2"><?php echo htmlspecialchars($article['title']); ?></h1>
                    <div class="mt-4 text-sm text-gray-500">
                        <span>By <?php echo htmlspecialchars($article['author']); ?></span> &bull;
                        <span>Published on <?php echo date('F j, Y', strtotime($article['created_at'])); ?></span>
                    </div>
                </div>

                <div class="mb-12 aspect-w-16 aspect-h-9 rounded-lg overflow-hidden shadow-lg">
                    <img src="/uploads/<?php echo htmlspecialchars($article['image']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" class="w-full h-full object-cover">
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
                    
                    <div class="lg:col-span-8">
                        <article class="prose prose-lg max-w-none font-source-serif text-gray-800 prose-p:leading-relaxed prose-a:text-blue-600 hover:prose-a:text-blue-800">
                            <?php echo nl2br($article['content']); ?>
                        </article>
                    </div>

                    <aside class="lg:col-span-4">
                        <div class="sticky top-24">
                            <h3 class="text-xl font-bold border-b-2 border-gray-200 pb-2 mb-4">Related Stories</h3>
                            <div class="space-y-4">
                                <?php if (!empty($related_articles)): ?>
                                    <?php foreach ($related_articles as $related): ?>
                                        <a href="/article/<?php echo $related['slug']; ?>" class="flex items-center gap-4 group">
                                            <div class="w-24 h-16 flex-shrink-0 bg-gray-100 rounded-md overflow-hidden">
                                                 <img src="/uploads/<?php echo htmlspecialchars($related['image']); ?>" class="w-full h-full object-cover">
                                            </div>
                                            <h4 class="font-semibold text-gray-800 group-hover:underline font-poppins"><?php echo htmlspecialchars($related['title']); ?></h4>
                                        </a>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-sm text-gray-500">No related articles found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-20 min-h-screen">
                <h1 class="text-4xl font-bold text-gray-800">404 - Article Not Found</h1>
                <p class="text-gray-600 mt-4">Sorry, the article you are looking for does not exist or may have been moved.</p>
                <a href="/index.php" class="mt-8 inline-block bg-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:bg-blue-700">Go to Homepage</a>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-800 text-white mt-12">
        <div class="container mx-auto px-4 py-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-xl font-bold mb-4">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></h3>
                    <p class="text-gray-400 text-sm mb-4">The latest news, delivered.</p>
                </div>
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">All Categories</h3>
                    <ul id="footer-categories" class="grid grid-cols-2 md:grid-cols-3 gap-y-2 gap-x-6 text-sm">
                        <?php foreach($all_categories as $category): ?>
                           <li><a href="/category/<?php echo urlencode(strtolower($category['name'])); ?>" class="text-white font-semibold hover:underline"><?php echo htmlspecialchars($category['name']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul id="footer-quick-links" class="space-y-2 text-sm mb-6">
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