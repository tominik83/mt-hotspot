<body>




	<div class="hs-wrapper">

		<div class="form-box login">

			<h2 id="hotspot_name" class="animate__animated animate__bounce">
				<?php echo esc_html(get_option('hotspot_ime', 'Hotspot')); ?>
			</h2>
			<form name="login" id="login" action="$(link-login-only)" method="post"
				onsubmit="$(if chap-id) return doLogin(); $(endif)">
				<input type="hidden" name="dst" value="$(link-orig)" />
				<input type="hidden" name="popup" value="true" />

				<div class="input-box">
					<label for="username">Username</label>
					<input name="username" type="text" value="" id="user_login" required />
					<i class='bx bxs-user'></i>
				</div>

				<div class="input-box">
					<label for="password">Password</label>
					<input name="password" type="password" id="user_pass" required />
					<i class='bx bxs-lock-alt'></i>
				</div>

				<!-- <input type="hidden" name="redirect_to" value="<?php echo home_url(); ?>"> -->
				<!-- <button type="submit">OK</button> -->
				<button type="submit" name="hs-submit" class="dugme">Login</button>

				<!-- <div class="logreg-link">
					<p>Don't have an account? <a href="#" class="register-link">Sign Up</a></p>
				</div> -->


			</form>

		</div>

		<div class="cms-logo">
			<?php
			the_custom_logo();
			if (is_front_page() && is_home()):
				?>
				<h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
						<?php bloginfo('name'); ?>
					</a></h1>
				<?php
			endif;

			
			?>			
		</div>

	</div>

</body>