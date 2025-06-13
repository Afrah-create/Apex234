<footer class="footer">
    <div class="container @auth-fluid @endauth">
        <nav>
            <ul class="footer-menu">
               
                <li>
                    <a href="#" class="nav-link" target="_blank">{{ __('Home') }}</a>
                </li>
                <li>
                    <a href="#" class="nav-link" target="_blank">{{ __('About Us') }}</a>
                </li>
               <li>
                    <a href="#" class="nav-link" target="_blank">{{ __('Contact Us') }}</a>
                </li>
            </ul>
            <p class="copyright text-center">
                ©
                <script>
                    document.write(new Date().getFullYear())
                </script>
                <a href="#">{{ __('Apex Developers') }}</a> 
            </p>
        </nav>
    </div>
</footer>