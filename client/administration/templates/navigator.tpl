<aside class="sidebar">
                <div class="scrollbar-inner">
                    <div class="user">
                        <div class="user__info" data-toggle="dropdown">
                            <img class="user__img" src="<?= $_SESSION['account']['avatar']; ?>" alt="">
                            <div>
                                <div class="user__name"><?= $_SESSION['account']['username']; ?></div>
                                <div class="user__email"><?= $Controller->Rank($_SESSION['account']['rank']); ?></div>
                            </div>
                        </div>

                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="../?logout=1">Déconnexion</a>
                        </div>
                    </div>

                    <ul class="navigation">
                        <li class="navigation__<?php if($page == 'accueil') { echo 'active'; } ?>"><a href="index"><i class="zmdi zmdi-home"></i> Accueil</a></li>

						<?php if($_SESSION['account']['rank'] >= 8) { ?>
                        <li class="navigation__sub <?php if($page == 'website' OR $page == 'payment') { echo 'navigation__active'; } ?>">
                            <a href=""><i class="zmdi zmdi-settings"></i> Configuration</a>

                            <ul>
                                <li class="@@sidebaractive"><a href="website">Site</a></li>
                                <li class="@@boxedactive"><a href="payment">Paiement</a></li>
                            </ul>
                        </li>
						<li class="navigation__sub <?php if($page == 'paiement_h' OR $page == 'historique_g') { echo 'navigation__active'; } ?>">
                            <a href=""><i class="zmdi zmdi-assignment"></i> Historiques</a>

                            <ul>
                                <li class="@@sidebaractive"><a href="his_payments">Paiement</a></li>
                                <li class="@@boxedactive"><a href="his_general">Général</a></li>
								<li class="@@pactive"><a href="his_admin">Admin</a></li>
                            </ul>
                        </li>
						<li class="navigation__<?php if($page == 'update') { echo 'active'; } ?>">
                            <a href="updates"><i class="zmdi zmdi-refresh"></i> Mises à jour</a>
                        </li>
						<?php } ?>
						<?php if($_SESSION['account']['rank'] >= 6) { ?>
						<li class="navigation__sub <?php if($page == 'user') { echo 'navigation__active'; } ?>">
                            <a href=""><i class="zmdi zmdi-accounts"></i> Clients</a>

                            <ul>
                                <li class="@@sidebaractive"><a href="search">Rechercher</a></li>
								<li class="@@pactive"><a href="ban">Banissement</a></li>
                            </ul>
                        </li>
						<li class="navigation__<?php if($page == 'commandes') { echo 'active'; } ?>"><a href="orders"><i class="zmdi zmdi-shopping-cart"></i> Commandes</a></li>
						<li class="navigation__<?php if($page == 'options') { echo 'active'; } ?>"><a href="optionsc"><i class="zmdi zmdi-folder"></i> Options de commande</a></li>
						<?php } ?>
						<li class="navigation__<?php if($page == 'redem') { echo 'active'; } ?>"><a href="rebootreinstall"><i class="zmdi zmdi-folder"></i> Redém/Réinstall</a></li>
						<li class="navigation__<?php if($page == 'tickets') { echo 'active'; } ?>"><a href="tickets"><i class="zmdi zmdi-ticket-star"></i> Tickets</a></li>
                    </ul>
                </div>
            </aside>