<?php if ( ! defined( 'ABSPATH' ) ) exit;

///////////////////////////////////////
// get $quiz_id from shortcode attribs
$quiz_post = get_post($quiz_id);

// Quit if not a quiz
if( !$quiz_post || $quiz_post->post_type != Qzy_Quiz_CPT::get_post_type_name() ){
	?>
	<p>No such Quiz! <?php echo $quiz_post->post_type; ?></p>
	<?php
	return;
}


$passed_questions_ids = array();

// Question template for logged users
$current_user = wp_get_current_user();
$qurrent_user_quiz = get_user_meta($current_user->ID, 'quizy_current_quiz', true);

if( $qurrent_user_quiz && $qurrent_user_quiz['id'] == $quiz_id ){

	if( array_key_exists('questions', $_POST) ){
		// fill answers from previous question
		$previous_question_id = $_POST['questions'][0];

		if( array_key_exists('answer', $_POST) ){
			$previous_answers_ids = $_POST['answer'][$previous_question_id];
		}else{
			$previous_answers_ids = array();
		}

		$qurrent_user_quiz['answer'][$previous_question_id] = $previous_answers_ids;

		if( !in_array($previous_question_id, $qurrent_user_quiz['questions']) ){
			$qurrent_user_quiz['questions'][] = $previous_question_id;
		}

		update_user_meta($current_user->ID, 'quizy_current_quiz', $qurrent_user_quiz);
	}

	foreach ($qurrent_user_quiz['questions'] as $question_id ) {
		array_push($passed_questions_ids, $question_id);
	}

}else{
	$qurrent_user_quiz = array(
		'id' => $quiz_id,
		'questions' => array(),
		'answer' => array()
		);
	if(!update_user_meta($current_user->ID, 'quizy_current_quiz', $qurrent_user_quiz)){
		add_user_meta($current_user->ID, 'quizy_current_quiz', $qurrent_user_quiz);
	}
}

/*
quizy_current_quiz =>
	id => 1256,
	questions => array(
		365,
		360,
		300
	)
	answers =>
		365 =>array(
			0,
			3
			)
		360 =>array(
			1,
			2
			)
		300 =>array(
			3
			)

quizy_evaluation =>
	1256 => 50.25,
	2254 => 100
*/

$cats = get_the_terms($quiz_id,'quiz_cat');
$cats_array = array();

if($cats){
	foreach ($cats as $key => $cat) {
		array_push($cats_array, $cat->name);
	}
}

$quiz_meta = get_post_meta( $quiz_id );

$quiz_type = ($quiz_meta['type'][0] ? $quiz_meta['type'][0] : get_option('qzy_default_quiz_type'));
$quiz_duration_per_question = ($quiz_meta['duration'][0] ? $quiz_meta['duration'][0] : get_option('qzy_default_duration'));
$max_questions_per_quiz = ($quiz_meta['questions_nbr'][0] ? $quiz_meta['questions_nbr'][0] : get_option('qzy_default_questions'));

?>
<div class="quiz_wrap">
	<?php

	$args = array(
			'post_type' => Qzy_Question_CPT::get_post_type_name(),
			'posts_per_page' => 1,
			'meta_key' => 'quiz_related',
	 		'orderby' => 'rand',
	 		'post__not_in' => $passed_questions_ids,
			'meta_query' => array(
				'key' => 'quiz_related',
				'value' => $quiz_id,
				'compare' => '='
				)
		);

	$questions = get_posts($args);

	// if evaluation
	if( count($qurrent_user_quiz['answer']) > $max_questions_per_quiz-1
		||
		!$questions
	 ){
		require QUIZY_TEMPLATES_DIR.'/quiz-evaluation.php';
	}else{
		$question = $questions[0];

		$nbr_question = 0;

		$qurrent_user_evaluation = get_user_meta($current_user->ID, 'quizy_evaluation', true);

		// Show last evaluation for this quiz for the current user
		if( $qurrent_user_evaluation && array_key_exists($quiz_id, $qurrent_user_evaluation) && !array_key_exists('questions', $_POST) ){
			?>
			<div class="result">Last result : <?php echo $qurrent_user_evaluation[$quiz_id]; ?>%</div>
			<?php
		}

	?>
		<div class="questions-wrap">
			<form action="" method="post">
				<?php require QUIZY_TEMPLATES_DIR.'/question-content.php'; ?>
				<input type="submit" value="Send">
			</form>
		</div>	
	<?php
	}
	?>

</div>