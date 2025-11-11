<?php
// Include the database configuration file.
require_once 'config.php';

// --- Fetch ALL Categories for Navbar & Footer ---
$categories_result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
$all_categories = $categories_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9641979331084216"
     crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - TCL NEWS</title>

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
                <a href="/index" class="text-2xl font-bold flex-shrink-0 flex items-center">TCL <span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ff0000;">N</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffa500;">E</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #ffff00;">W</span><span style="font-family: Arial, sans-serif; font-weight: 700; font-style: italic; color: #008000;">S</span></a>
                <div class="hidden md:flex flex-1 justify-center items-center overflow-hidden mx-4">
                    <div id="desktop-nav-links" class="flex items-center space-x-4 text-sm overflow-x-auto no-scrollbar whitespace-nowrap">
                        <a href="/index" class="font-semibold flex-shrink-0">Home</a>
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
        <div class="bg-white p-8 rounded-lg shadow-md max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold text-gray-800 mb-6 border-l-4 border-blue-600 pl-4 font-poppins">About TCL NEWS</h1>
            
            <div class="prose max-w-none text-gray-700">
                <p class="lead text-lg mb-6">Welcome to TCL NEWS, your premier destination for reliable, timely, and engaging news content from around the globe. We are dedicated to bringing you the stories that matter, covering everything from breaking headlines to in-depth analyses across a multitude of categories.</p>
                <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Our Mission</h2>
                <p>In an age of information overload, our mission is to cut through the noise and provide clarity. We believe in the power of journalism to inform, educate, and inspire. Our goal is to empower our readers with accurate, unbiased, and comprehensive news, enabling them to form their own opinions and stay connected with the world around them.</p>
                <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Our Values</h2>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>Integrity:</strong> We adhere to the highest ethical standards of journalism. Accuracy, fairness, and impartiality are the cornerstones of our reporting.</li>
                    <li><strong>Credibility:</strong> We are committed to earning and maintaining your trust through meticulous fact-checking and transparent sourcing.</li>
                    <li><strong>Clarity:</strong> We strive to present complex issues in a clear, concise, and accessible manner, without sacrificing depth or context.</li>
                    <li><strong>Community:</strong> We aim to foster a community of informed citizens who are passionate about the world and engaged in constructive dialogue.</li>
                </ul>
                <h2 class="text-2xl font-bold text-gray-800 mt-8 mb-4">Our Team</h2>
                <p>Behind TCL NEWS is a dedicated team of experienced journalists, editors, and technologists who are passionate about storytelling and committed to journalistic excellence. We come from diverse backgrounds but share a common goal: to deliver news that you can trust.</p>
                <p class="mt-8">Thank you for choosing TCL NEWS. We are honored to be your source for news and information.</p>
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
</body>
</html>