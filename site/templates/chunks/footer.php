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