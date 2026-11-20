<?php
/**
 * Global full-screen search overlay and editable popular topics.
 *
 * @package Archdiocese_of_Baltimore
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$topics = array();
if ( function_exists( 'get_field' ) ) {
	$saved = get_field( 'balt_search_topics', 'option' );
	if ( is_array( $saved ) ) {
		foreach ( $saved as $topic ) {
			$label = trim( (string) ( $topic['label'] ?? '' ) );
			if ( $label ) {
				$topics[] = array(
					'label' => $label,
					'link'  => is_array( $topic['link'] ?? null ) && ! empty( $topic['link']['url'] ) ? $topic['link'] : null,
				);
			}
		}
	}
}
if ( ! $topics ) {
	foreach ( array( 'My Faith', 'My Marriage', 'Careers', 'Young Adult Opportunities', 'Baptism', 'Vocations' ) as $label ) {
		$topics[] = array( 'label' => $label, 'link' => null );
	}
}
?>
<dialog id="balt-live-search-dialog" class="balt-search-dialog" aria-labelledby="balt-search-heading" aria-describedby="balt-search-intro" data-endpoint="<?php echo esc_url( rest_url( 'balt/v1/live-search' ) ); ?>">
	<button type="button" class="balt-search-close" data-balt-search-close aria-label="<?php esc_attr_e( 'Close search', 'baltimore' ); ?>">
		<span><?php esc_html_e( 'Close', 'baltimore' ); ?></span>
		<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5l14 14M19 5L5 19"/></svg>
	</button>
	<div class="balt-search-card">
		<div class="balt-search-heading-row">
			<div>
				<p class="balt-search-eyebrow"><?php esc_html_e( 'Archdiocese Knowledge Base', 'baltimore' ); ?></p>
				<h2 id="balt-search-heading" class="balt-search-heading"><?php esc_html_e( 'How Can We Help You Today?', 'baltimore' ); ?></h2>
			</div>
			<p id="balt-search-intro" class="balt-search-intro"><?php esc_html_e( 'If you are looking for a specific resource or the answer to a question, our knowledge base can connect you with the information you need.', 'baltimore' ); ?></p>
		</div>
		<form class="balt-search-form" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" data-balt-search-form>
			<label class="sr-only" for="balt-live-search-input"><?php esc_html_e( 'Search the Archdiocese', 'baltimore' ); ?></label>
			<svg class="balt-search-icon" viewBox="0 0 24 24" width="20" height="20" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="10.8" cy="10.8" r="6.8"/><path d="m16 16 4.5 4.5"/></svg>
			<input id="balt-live-search-input" class="balt-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search here...', 'baltimore' ); ?>" autocomplete="off" autocapitalize="off" spellcheck="false" maxlength="100" role="combobox" aria-autocomplete="list" aria-haspopup="listbox" aria-controls="balt-search-results" aria-expanded="false" data-balt-search-input>
			<button type="button" class="balt-search-clear" data-balt-search-clear aria-label="<?php esc_attr_e( 'Clear search', 'baltimore' ); ?>" hidden>
				<svg viewBox="0 0 24 24" width="14" height="14" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M5 5l14 14M19 5L5 19"/></svg>
			</button>
		</form>
		<div class="balt-search-popular" data-balt-search-popular>
			<p><?php esc_html_e( 'I want to learn more about...', 'baltimore' ); ?></p>
			<div class="balt-search-popular-links">
				<?php foreach ( array_slice( $topics, 0, 12 ) as $topic ) : ?>
					<?php if ( $topic['link'] ) : ?>
						<a href="<?php echo esc_url( $topic['link']['url'] ); ?>"<?php if ( '_blank' === ( $topic['link']['target'] ?? '' ) ) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html( $topic['label'] ); ?></a>
					<?php else : ?>
						<button type="button" data-balt-search-topic="<?php echo esc_attr( $topic['label'] ); ?>"><?php echo esc_html( $topic['label'] ); ?></button>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="balt-search-feedback" data-balt-search-feedback hidden>
			<p class="balt-search-hint" data-balt-search-hint hidden><?php esc_html_e( 'Press ↓ to navigate / Enter to select', 'baltimore' ); ?></p>
			<div id="balt-search-results" class="balt-search-results" role="listbox" aria-label="<?php esc_attr_e( 'Search suggestions', 'baltimore' ); ?>" data-balt-search-results></div>
			<p class="balt-search-message" data-balt-search-message hidden></p>
		</div>
		<p class="sr-only" data-balt-search-announcement role="status" aria-live="polite" aria-atomic="true"></p>
	</div>
</dialog>
