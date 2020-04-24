<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var Tools $tools */
/** @var SiteSettings $settings */

include "chunks/init.php";
include "./chunks/dochead.php"; //all js goes in dochead

?>
<div class="full-width wc-padding-vertical">
	<div class="centreme">
		<div class="centremeinner ">
			<?= $page->body_content ?>
		</div>
	</div>
</div>

<!-- Form section -->
<div class="full-width wc-padding-vertical">
	<div class="uk-align-center uk-text-center">
		<div class="">
			<?= $page->body_content2 ?>
		</div>

		<form onsubmit='return false;' class='form-holder-div former'>
			<fieldset class="uk-fieldset contactForm form-subdiv uk-text-left" id="form-contact-fieldset" data-formid="1184" data-formtarget="form-handler-generic.php">
				<div class="uk-margin form-parent-div">
					<div class="form-input-parent text mand">
						<p class="inputlbl">I wish to... <span class="reqtext">*</span></p>
						<select class="select-form-input" data-label="Contact Type" id="form_contactType">
							<option value=""></option>
							<option value="AskAQuestion">Ask a question</option>
							<option value="ShareCompliment">Share a compliment</option>
							<option value="ExpressComplaint">Express a complaint</option>
						</select>
					</div>
				</div>
				<div class="uk-margin form-parent-div">
					<div class="form-input-parent text mand">
						<p class="inputlbl">Your name <span class="reqtext">*</span></p>
						<input type="text" class="text-form-input" data-label="User Name" />
					</div>
				</div>
				<div class="uk-margin form-parent-div">
					<div class="form-input-parent text mand">
						<p class="inputlbl">Email address <span class="reqtext">*</span></p>
						<input type="email" class="text-form-input" data-label="Email Address" data-useremail="1" />
					</div>
				</div>
				<div class="uk-margin form-parent-div">
					<div class="form-input-parent text mand">
						<p class="inputlbl">Contact number <span class="reqtext">*</span></p>
						<input type="text" class="text-form-input" data-label="Contact Phone" />
					</div>
				</div>
				<div class="uk-margin form-parent-div">
					<div class="form-input-parent text-area mand">
						<p class="inputlbl">Message <span class="reqtext">*</span></p>
						<textarea data-label="Message"></textarea>
					</div>
				</div>
				<div>
					<p class="quote-form-inner-title">Type of service(s)</p>
					<p><input data-label="serviceHeatReflectiveCoating" type="checkbox" id="HeatReflectiveCoating" /><label for="HeatReflectiveCoating" class="quote-checklabel">Heat Reflective Coating</label> </p>
					<p><input data-label="serviceRoofRepaint" type="checkbox" id="RoofRepaint" /><label for="RoofRepaint" class="quote-checklabel">Roof Repaint</label> </p>
					<p><input data-label="serviceRoofRescrewing" type="checkbox" id="RoofRescrewing" /><label for="RoofRescrewing" class="quote-checklabel">Roof Re-scewing</label> </p>
				</div>
				<input type="text" style="display: none;" id="form_robots" class="robotfield" />
				<?php if($user->isSuperuser()) { ?>
					<button class="" onclick="testFillForm(this);">TestFill</button>
				<?php } ?>
				<button class='formsubmitbtn' onclick="submitForm(this);">Submit</button>
			</fieldset>
		</form>
	</div>
</div>

<!-- Map that can be relocated if needed -->
<div class="full-width wc-padding-vertical">
	<div class="centreme">
		<div class="centremeinner">
			<script>
				var latitude = -37.743000;
				var longitude = 145.034085;
				if(<?= $settings->latitude ?> != "") {
					latitude = parseFloat("<?= $settings->latitude ?>");
				}
				if(<?= $settings->longitude ?> != "") {
					longitude = parseFloat("<?= $settings->longitude ?>");
				}
				<?php $apikey = "AIzaSyBd8tRd9zxVZFijVqpROs8ImM7e440yECs"; //this is the ACE ProcessWire API Key by default
					if($settings->gmaps_apikey != "") {
						$apikey = $settings->gmaps_apikey;
					}
				?>
				function initMap() { //latitude, longitude, gmaps_apikey
					var office = {lat: latitude, lng: longitude}; // The location of office
					var map = new google.maps.Map(
					document.getElementById('mapcontact'), {zoom: 8, center: office});
					var marker = new google.maps.Marker({position: office, map: map}); //the marker - duplicate this line if needed for more markers
				}
			</script>
			<script async defer src="<?php echo 'https://maps.googleapis.com/maps/api/js?key='.$apikey.'&callback=initMap'; ?>"></script>
			<div id="mapcontact" style="height: 100%; width: 100%;"></div>
		</div>
	</div>
</div>

<?php include "./chunks/footer.php"; //homewrap div is closed in footer ?>
