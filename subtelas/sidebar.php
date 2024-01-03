<aside class="menu-sidebar d-none d-lg-block">
            <div class="logo">
                <a href="index">
                    <img src="images/icon/Logo-Vetorian-Horizontal-Color.png" alt="vetorian" />
                </a>
            </div>
            <div class="menu-sidebar__content js-scrollbar1">
                <nav class="navbar-sidebar">
                    <ul class="list-unstyled navbar__list">
                        <li class="index">
                            <a class="js-arrow" href="index">
                                <i class="fas fa-globe"></i>Tela inicial</a>
                        </li>
                        <li class="calendario">
                            <a href="calendario">
                                <i class="zmdi zmdi-calendar"></i> Calendario
                            </a>
                        </li>
                        <li class="kanban">
                            <a href="kanban">
                                <i class="zmdi zmdi-receipt"></i>Kanban</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>


        <script>
            var currentPath = window.location.pathname;
            var liIndex = document.getElementsByClassName('index')[0];
            var liCalendario = document.getElementsByClassName('calendario')[0];
            var liKanban = document.getElementsByClassName('kanban')[0];

            // console.log(currentPath);
            
            if(currentPath === '/engedoc_rapha/index' || currentPath === '/engedoc_rapha/index.php' || currentPath === '/engedoc_rapha/'){
                liIndex.classList.add('active');
            }else if(currentPath === '/engedoc_rapha/calendario' || currentPath === '/engedoc_rapha/calendario.php' ){
                liCalendario.classList.add('active');
            }else if(currentPath === '/engedoc_rapha/kanban' || currentPath === '/engedoc_rapha/kanban.php'){
                liKanban.classList.add('active');
            }
        </script>