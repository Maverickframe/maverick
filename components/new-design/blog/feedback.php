<?php
/**
 * "Was this helpful?" feedback at the bottom of the TOC sidebar.
 * State is persisted in localStorage so the same reader doesn't get re-prompted.
 * No backend tracking yet — purely UX signal for the user.
 */
$postId = get_the_ID();
?>
<div class="feedback" data-feedback data-post-id="<?= (int) $postId; ?>">
    <div class="feedback__prompt" data-feedback-prompt>
        <p class="feedback__title">Was this helpful?</p>
        <div class="feedback__buttons">
            <button type="button" class="feedback__btn" data-feedback-vote="up" aria-label="Yes, helpful">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M14 9V5a3 3 0 0 0-6 0v4"/>
                    <path d="M14 9h4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H10a2 2 0 0 1-2-2V9"/>
                    <path d="M8 9H4v12h4"/>
                </svg>
                <span>Yes</span>
            </button>
            <button type="button" class="feedback__btn" data-feedback-vote="down" aria-label="No, not helpful">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M10 15v4a3 3 0 0 0 6 0v-4"/>
                    <path d="M10 15H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v10"/>
                    <path d="M16 15h4V3h-4"/>
                </svg>
                <span>No</span>
            </button>
        </div>
    </div>
    <div class="feedback__thanks" data-feedback-thanks hidden>
        <p class="feedback__title">Thanks for the feedback!</p>
    </div>
</div>
