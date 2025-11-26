<?php

namespace App\Helpers;

class MarkdownRenderer
{
    /**
     * Render basic markdown-like text with proper formatting
     * Supports:
     * - Numbered lists (1. 2. 3.)
     * - Bullet points (-, *, •)
     * - Bold text (**text**)
     * - Line breaks
     */
    public static function renderNotes($text)
    {
        if (empty($text)) {
            return '';
        }

        // Split into lines for processing
        $lines = explode("\n", $text);
        $html = '';
        $inNumberedList = false;
        $inBulletList = false;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) {
                // Handle line breaks
                if ($inNumberedList) {
                    $html .= '</ol>';
                    $inNumberedList = false;
                }
                if ($inBulletList) {
                    $html .= '</ul>';
                    $inBulletList = false;
                }
                $html .= '<br>';
                continue;
            }

            // Check for numbered lists (1. 2. 3. etc.)
            if (preg_match('/^\d+\.\s+(.+)/', $line, $matches)) {
                if (!$inNumberedList) {
                    if ($inBulletList) {
                        $html .= '</ul>';
                        $inBulletList = false;
                    }
                    $html .= '<ol class="notes-list">';
                    $inNumberedList = true;
                }
                $content = self::processInlineFormatting($matches[1]);
                $html .= '<li>' . $content . '</li>';
                continue;
            }

            // Check for bullet points (-, *, •)
            if (preg_match('/^[-*•]\s+(.+)/', $line, $matches)) {
                if (!$inBulletList) {
                    if ($inNumberedList) {
                        $html .= '</ol>';
                        $inNumberedList = false;
                    }
                    $html .= '<ul class="notes-list">';
                    $inBulletList = true;
                }
                $content = self::processInlineFormatting($matches[1]);
                $html .= '<li>' . $content . '</li>';
                continue;
            }

            // Close any open lists if we hit regular text
            if ($inNumberedList || $inBulletList) {
                if ($inNumberedList) {
                    $html .= '</ol>';
                    $inNumberedList = false;
                }
                if ($inBulletList) {
                    $html .= '</ul>';
                    $inBulletList = false;
                }
            }

            // Regular paragraph
            $content = self::processInlineFormatting($line);
            $html .= '<p class="notes-paragraph">' . $content . '</p>';
        }

        // Close any remaining open lists
        if ($inNumberedList) {
            $html .= '</ol>';
        }
        if ($inBulletList) {
            $html .= '</ul>';
        }

        return $html;
    }

    /**
     * Process inline formatting like bold text
     */
    private static function processInlineFormatting($text)
    {
        // Bold text **text**
        $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
        
        // Italic text *text*
        $text = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $text);
        
        return $text;
    }

    /**
     * Sanitize text for safe HTML rendering
     */
    public static function sanitize($text)
    {
        // Basic HTML escaping
        $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        
        // Allow some basic HTML tags for formatting
        $allowedTags = '<strong><em><br><p><ul><ol><li>';
        $text = strip_tags($text, $allowedTags);
        
        return $text;
    }

    /**
     * Quick rendering method that combines sanitization and rendering
     */
    public static function render($text)
    {
        $sanitized = self::sanitize($text);
        return self::renderNotes($sanitized);
    }
}