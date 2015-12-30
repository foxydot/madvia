<?php global $wpalchemy_media_access; ?>
<div class="my_meta_control gform_wrapper">
<ul class='gform_fields top_label description_below'>
<?php $mb->the_field('youtube'); ?>
<li class='gfield' ><label class='gfield_label'>YouTube URL</label>
<div class='ginput_container'><input name='<?php $mb->the_name(); ?>' type='text' value='<?php $mb->the_value(); ?>' class='medium'  tabindex='3'  /></div></li>
</ul>
<ul class='gform_fields top_label description_below'>
<?php $mb->the_field('website'); ?>
<li class='gfield' ><label class='gfield_label'>Website URL</label>
<div class='ginput_container'><input name='<?php $mb->the_name(); ?>' type='text' value='<?php $mb->the_value(); ?>' class='medium'  tabindex='3'  /></div></li>
</ul>
<h4>Multiple Image Entries</h4>

	<p>Use this area to create multiple entry portfolio items. Mostly useful for Digital portfolio items where screenshots of multiple pages are to be displayed. Upload new documents using the "Add Media" box.</p>
 	<div class="table">
 	<?php $i = 0; ?>
	<?php while($mb->have_fields_and_multi('multientry')): ?>
	<?php $mb->the_group_open(); ?>
	<div class="row <?php print $i%2==0?'even':'odd'; ?>">
 		<div class="cell">
 		<label>Title</label>
		<?php $mb->the_field('title'); ?>
		<input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/>
		</div>
 		<div class="cell">
 		<label>Description</label>
		<?php $mb->the_field('description'); ?>
		<textarea name="<?php $mb->the_name(); ?>"><?php $mb->the_value(); ?></textarea>
		</div>
 		<div class="cell">
 		<label>Image URL</label>
		<?php $mb->the_field('image'); ?>
        <?php $wpalchemy_media_access->setGroupName('image'. $mb->get_the_index())->setInsertButtonLabel('Insert This')->setTab('gallery'); ?>
		
		<?php echo $wpalchemy_media_access->getField(array('name' => $mb->get_the_name(), 'value' => $mb->get_the_value(), 'style' => 'width: 90%')); ?>
        <?php echo $wpalchemy_media_access->getButton(array('label' => '+')); ?>
		</div>
 		<div class="cell">
 			<a href="#" class="dodelete button">Remove Image</a>
		</div>
		<hr />
 	</div>
 	<?php $i++; ?>
	<?php $mb->the_group_close(); ?>
	<?php endwhile; ?>
 	</div>
	<p style="margin-bottom:15px; padding-top:5px;"><a href="#" class="docopy-multientry button">Add Entry</a>
	<a href="#" class="dodelete-multientry button">Remove All Entries</a></p>
	

</div>