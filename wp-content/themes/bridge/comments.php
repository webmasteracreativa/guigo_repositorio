<div class="comment_holder clearfix" id="comments">
<div class="comment_number"><div class="comment_number_inner"><h5><?php comments_number( __('No Comments','qode'), '1 '.__('Comment','qode'), '% '.__('Comments','qode')); ?></h5></div></div>
<div class="comments">
<?php if ( post_password_required() ) : ?>
				<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'qode' ); ?></p>
			</div></div>
<?php
		
		return;
	endif;
?>
<?php if ( have_comments() ) : ?>

	<ul class="comment-list">
		<?php wp_list_comments( array_unique( array_merge( array( 'callback' => 'qode_comment' ), apply_filters( 'qode_comments_callback', array() ) ) ) ); ?>
	</ul>


<?php // End Comments ?>

 <?php else : // this is displayed if there are no comments so far 

	if ( ! comments_open() ) :
?>
		<!-- If comments are open, but there are no comments. -->

	 
		<!-- If comments are closed. -->
		<p><?php _e('Sorry, the comment form is closed at this time.', 'qode'); ?></p>

	<?php endif; ?>
<?php endif; ?>
</div></div>
<?php
$commenter = wp_get_current_commenter();
$req = get_option( 'require_name_email' );
$aria_req = ( $req ? " aria-required='true'" : '' );
$consent  = empty( $commenter['comment_author_email'] ) ? '' : ' checked="checked"';
$args = array(
	'id_form' => 'commentform',
	'id_submit' => 'submit_comment',
	'title_reply'=>'<h5>'. __( 'Post A Comment','qode' ) .'</h5>',
	'title_reply_to' => __( 'Post A Reply to %s','qode' ),
	'cancel_reply_link' => __( 'Cancel Reply','qode' ),
	'label_submit' => __( 'Submit','qode' ),
	'comment_field'        => apply_filters( 'qode_comment_form_textarea_field', '<textarea id="comment" placeholder="' . esc_html__( 'Write your comment here...', 'qode' ) . '" name="comment" cols="45" rows="8" aria-required="true"></textarea>' ),
	'comment_notes_before' => '',
	'comment_notes_after' => '',
	'fields' => apply_filters( 'comment_form_default_fields', array(
		'author' => '<div class="three_columns clearfix"><div class="column1"><div class="column_inner"><input id="author" name="author" placeholder="'. __( 'Your full name','qode' ) .'" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '"' . $aria_req . ' /></div></div>',
		'email' => '<div class="column2"><div class="column_inner"><input id="email" name="email" placeholder="'. __( 'E-mail address','qode' ) .'" type="text" value="' . esc_attr(  $commenter['comment_author_email'] ) . '"' . $aria_req . ' /></div></div>',
		'url' => '<div class="column3"><div class="column_inner"><input id="url" name="url" type="text" placeholder="'. __( 'Website','qode' ) .'" value="' . esc_attr( $commenter['comment_author_url'] ) . '" /></div></div></div>',
		'cookies' => '<p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . ' />' .
					'<label for="wp-comment-cookies-consent">' . __( 'Save my name, email, and website in this browser for the next time I comment.', 'qode' ) . '</label></p>',
		  ) ) );

$args = apply_filters( 'qode_comment_form_final_fields', $args );
 ?>
 <div class="comment_pager">
	<p><?php paginate_comments_links(); ?></p>
 </div>
 <div class="comment_form">
	<?php comment_form($args); ?>
</div>
						
								
							


