<?php if ( ! defined( 'ABSPATH' ) ) exit;

$answers = get_post_meta($question->ID,'answers', true);
foreach ($answers as $key => $answer) {
	$answers[$key] = array(
						'key' => $key,
	 					'answer' => $answer
	 				);
}

// Randomize answers elements
shuffle($answers);

$image_url = get_the_post_thumbnail_url( $question, 'post-thumbnail' );

?>

<div class="question">
	<h2><?php echo $question_num; ?>. <?php echo esc_html($question->post_content); ?></h2>
	<ol class="answers">
		<?php foreach ($answers as $key => $answer) { ?>
				<li>
					<label>
						<?php if( 'ucq' == $quiz_type ): ?>
							<input type="radio" name="answer[<?php echo $question->ID; ?>]" value="<?php echo $answer['key']; ?>">
						<?php else: ?>
							<input type="checkbox" name="answer[<?php echo $question->ID; ?>][<?php echo $answer['key']; ?>]" value="<?php echo $answer['key']; ?>">
						<?php endif; ?>
							<span><?php echo esc_html($answer['answer']) ; ?></span>
					</label>
				</li>
		<?php } ?>
	</ol>
	<?php if( $image_url ){ ?>
		<div class="thumb">
			<img src="<?php echo $image_url; ?>" alt="">
		</div>
	<?php } ?>
	<input type="hidden" name="questions[]" value="<?php echo $question->ID; ?>">
</div>