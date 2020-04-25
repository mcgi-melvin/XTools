<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */

//can use $pages->get("/block-elements/footer/"); if required
?>
<!-- Footer -->

	<div class="footcurvy">
		<svg viewBox="0 0 500 85" preserveAspectRatio="xMinYMin meet">
			<path d="M0,60 C100,100 200,90 500,40 L500,00 L0,0 Z" style="stroke: none; fill:rgba(0 2 47);" transform="scale(2,-2) translate(0,-100)"></path>
		</svg>
	</div>
<footer>

	<div class="footer-logo">
		<img src="" alt="">
	</div>


	<div class="footer-content">
		<nav>
			<ul>
				<li><a href="#"> Tools </a></li>
				<li><a href="#"> Tools </a></li>
				<li><a href="#"> Tools </a></li>
			</ul>
		</nav>
	</div>


</footer>

<script src="js/jquery-3.4.1.slim.min.js"></script>


<script>

$(function(){

	$('.tab-nav').on('click', function(){
		$('.tab-nav').removeClass('active');
		$(this).addClass('active')
		var clicktab = $(this).attr('rel');

		$('#panel .content').slideUp(1000, function(){
			$('#panel .content').removeClass('active');

			$('#'+clicktab).slideDown(1000, function(){
				$(this).addClass('active');

			});
		});
	});
});

</script>




<div class="full-width">
	<div class="centreme">
		<div class="centremeinner">
			<p>&copy;<?php echo date("Y",time()); ?></p>
		</div>
	</div>
</div>

<!-- End Footer -->
</div> <!-- end off-canvas-content div from dochead -->
</body>
</html>
