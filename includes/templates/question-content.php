<?php
$qst_user_ansers = array();

if( array_key_exists('answer', $_POST) &&  array_key_exists($question->ID, $_POST['answer']) ){
	$qst_user_ansers = $_POST['answer'][$question->ID]; // user answers for the current question 
}

$answers = get_post_meta($question->ID,'answers', true);

$goods = get_post_meta($question->ID,'goods', true);

?>
<h2><?php echo esc_html($question->post_content); ?></h2>
<ul class="answers">
	<?php foreach ($answers as $key => $answer) { ?>
			<?php 
				if( 
					( 
					(is_array($qst_user_ansers) && in_array($key, $qst_user_ansers) ) || (!is_array($qst_user_ansers) && $key == $qst_user_ansers ) )
					 && array_key_exists($key, $goods)
					 ){
					$choice_eval = '<span style="color:green;">Good choice</span>';
				}else if( 
					(
						(is_array($qst_user_ansers) && in_array($key, $qst_user_ansers) ) || ( !is_array($qst_user_ansers) && $key ==$qst_user_ansers ) )
					&& !array_key_exists($key, $goods) 
					){
					$choice_eval = '<span style="color:red;">Bad choice</span>';
				}else{
					$choice_eval = '';
				}
			?>
			<li>
				<label><?php echo  $choice_eval; ?>
					<?php if( 'ucq' == $quiz_type ): ?>
						<input type="radio" name="answer[<?php echo $question->ID; ?>]" value="<?php echo $key; ?>">
					<?php else: ?>
						<input type="checkbox" name="answer[<?php echo $question->ID; ?>][<?php echo $key; ?>]" value="<?php echo $key; ?>">
				<?php endif; ?>
					<span><?php echo esc_html($answer) ; ?></span>
				</label>
			</li>
	<?php } ?>
</ul>
<input type="hidden" name="questions[]" value="<?php echo $question->ID; ?>">