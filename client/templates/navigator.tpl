<aside class="sidebar">
		<div class="left-aside-container">
			<div class="user-profile-container">
				<div class="user-profile clearfix">
					<div class="admin-user-thumb">
						<img src="<?= $_SESSION['account']['avatar']; ?>" class="img-circle">
					</div>
					<div class="admin-user-info">
						<ul class="user-info">
							<li><a href="#" class="<?= $Controller->RankColor($_SESSION['account']['rank']); ?> text-semibold text-size-large"><?= $_SESSION['account']['username']; ?></a></li>
							<li><a href="#"><small style="color: #fff"><?= $Controller->Rank($_SESSION['account']['rank']); ?></small></a></li>
						</ul>
						<div class="logout-icon"><a href="?logout=1"><i class="icon-exit2"></i></a></div>
					</div>
					
				</div>				
			</div>						
			<ul class="sidebar-accordion">			
				<li class="list-title">Navigation</li>
				<li <?php if($page_open == 'accueil') { echo 'class="active"'; } ?>>
					<a href="index"><i class="icon-home2"></i><span class="list-label"> Accueil</span></a>
				</li>
				<li <?php if($page_open == 'commander') { echo 'class="active"'; } ?>>
					<a href="orders"><i class="icon-cart-add"></i><span class="list-label"> Commander</span></a>
				</li>
				<li <?php if($page_open == 'commandesenattente') { echo 'class="active"'; } ?>>
					<a href="current_orders"><i class="icon-cart"></i><span class="list-label"> Mes commandes</span></a>
				</li>
				<li <?php if($page_open == 'services') { echo 'class="active"'; } ?>>
					<a href="#" class="acc-parent"><i class="icon-briefcase3"></i><span>Mes services</span><span class="acc-icon"></span></a>
					<ul style="display: none;">
						<li><a href="retros_habbo">Mes rétros-habbo</a></li>																					
						<li><a href="domains">Mes domaines</a></li>					
					</ul>
				</li>
				<li <?php if($page_open == 'rechargement') { echo 'class="active"'; } ?>>
					<a href="reloading"><i class="icon-wallet"></i><span class="list-label"> Rechargement</span></a>
				</li>
				<li <?php if($page_open == 'equipe') { echo 'class="active"'; } ?>>
					<a href="staffs"><i class="icon-users"></i><span class="list-label"> Équipe</span></a>
				</li>
				<li class="list-title">Support</li>
				<li <?php if($page_open == 'consulter') { echo 'class="active"'; } ?>>
					<a href="support"><i class="icon-comment-discussion"></i><span class="list-label"> Consulter mes tickets</span></a>
				</li>
				<li <?php if($page_open == 'create') { echo 'class="active"'; } ?>>
					<a href="support?page=create"><i class="icon-plus3"></i><span class="list-label"> Créer un ticket</span></a>
				</li>
				<li class="list-title">Divers</li>
				<li <?php if($page_open == 'boutique') { echo 'class="active"'; } ?>>
					<a href="shop"><i class="icon-bag"></i><span class="list-label"> Boutique</span></a>
				</li>
				<?php if($_SESSION['account']['rank'] >= 2) { ?>
				<li <?php if($page_open == 'forum') { echo 'class="active"'; } ?>>
					<a href="forum"><i class="icon-bubbles5"></i><span class="list-label"> Forum</span></a>
				</li>
				<?php } ?>
				<?php if($_SESSION['account']['rank'] >= 3) { ?>
				<li <?php if($page_open == 'loto') { echo 'class="active"'; } ?>>
					<a href="loto"><i class="icon-coins"></i><span class="list-label"> Loto</span></a>
				</li>
				<?php } ?>
				<li <?php if($page_open == 'maj') { echo 'class="active"'; } ?>>
					<a href="updates"><i class="icon-folder-open2"></i><span class="list-label"> Mises à jour</span></a>
				</li>
				<?php if($_SESSION['account']['rank'] >= 5) { ?>
				<li>
					<a href="administration/"><i class="icon-key"></i><span class="list-label"> Administration</span></a>
				</li>
				<?php } ?>
			</ul>	
		</div>
	</aside>