<footer class="bg-black text-gray-400 flex flex-col justify-center gap-10">
    <div class="max-w-[1400px] mx-auto">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5 pt-20 pb-10 px-5 sm:px-10">
            <div>
                <h1 class="text-4xl text-indigo-400 font-semibold mb-5"><img class="w-36" src="Images/Screenshot_2024-08-18_112444-removebg-preview.png" alt="Logo"></h1>
                <p>Discover VELIRO's dedication to elegance, comfort, and the art of dressing well.</p>
                <ul class="flex items-center gap-7 text-xl mt-3">
                    <li class="hover:text-rose-700 transition-colors duration-200">
                        <a href="#"><i class="ri-instagram-line"></i></a>
                    </li>
                    <li class="hover:text-blue-700 transition-colors duration-200">
                        <a href="#"><i class="ri-facebook-circle-fill"></i></a>
                    </li>
                    <li class="hover:text-gray-700 transition-colors duration-200">
                        <a href="#"><i class="ri-twitter-x-line"></i></a>
                    </li>
                    <li class="hover:text-red-700 transition-colors duration-200">
                        <a href="#"><i class="ri-youtube-fill"></i></a>
                    </li>
                </ul>

                <!-- Google Translate -->
                <div id="google_translate_element" class="mt-3"></div>
                <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElement"></script>

                <script type="text/javascript">
                    function googleTranslateElement() {
                        new google.translate.TranslateElement({
                            pageLanguage: 'en'
                        }, 'google_translate_element');
                    }
                </script>
            </div>
            <div>
                <h1 class="text-2xl text-white mb-3">Information</h1>
                <ul>
                    <li>
                        <a class="hover:underline" href="AboutUs.php">About us</a>
                    </li>
                    <li>
                        <a class="hover:underline" href="Contact.php">Contact</a>
                    </li>
                </ul>
            </div>
            <div>
                <h1 class="text-2xl text-white mb-3">Discover</h1>
                <ul>
                    <li>
                        <a class="hover:underline" href="Accessories.php">Accessories</a>
                    </li>
                    <li>
                        <a class="hover:underline" href="Shirts.php">Shirts</a>
                    </li>
                    <li>
                        <a class="hover:underline" href="Bottoms.php">Bottoms</a>
                    </li>
                    <li>
                        <a class="hover:underline" href="JacketsAndCoats.php">Jackets & Coats</a>
                    </li>
                    <li>
                        <a class="hover:underline" href="Shoes.php">Footwear</a>
                    </li>
                </ul>
            </div>
            <div>
                <h1 class="text-2xl text-white mb-3">Locate Us</h1>
                <ul>
                    <li>No (15) Ground floor , 49 lower street, Merchant Rd, 11161</li>
                    <li>Yangon, Myanmar</li>
                    <li>+1 123-456-7890</li>
                    <li>mail@veliro.com</li>
                </ul>
            </div>
        </div>
        <div class="flex flex-col-reverse sm:flex-row justify-between border-t border-gray-800 gap-3 sm:gap-0 py-10 px-10">
            <p>© <span id="year"></span> VELIRO. Powered by VELIRO</p>
            <ul class="flex gap-2 select-none">
                <li>
                    <img src="Images/fashion-designer-cc-visa-icon.svg" alt="Icon">
                </li>
                <li>
                    <img src="Images/fashion-designer-cc-mastercard-icon.svg" alt="Icon">
                </li>
                <li>
                    <img src="Images/fashion-designer-cc-discover-icon.svg" alt="Icon">
                </li>
                <li>
                    <img src="Images/fashion-designer-cc-apple-pay-icon.svg" alt="Icon">
                </li>
            </ul>
        </div>
    </div>
</footer>

<script>
    // Get Year
    const getDate = new Date();
    const getYear = getDate.getFullYear();

    document.getElementById('year').textContent = getYear;
</script>