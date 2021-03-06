<?php
/**
 * @package DrooPHP
 * @author Patrick Dawkins <pjcdawkins@gmail.com>
 */

namespace DrooPHP\Formatter;

use \DrooPHP\Candidate;

/**
 * An HTML output format.
 */
class Html extends FormatterBase
{

    /**
     * Overrides parent::getDefaultOptions().
     */
    public function getDefaultOptions() {
        return array(
            'html_fragment' => FALSE,
        );
    }

    /**
     * @see FormatterInterface::getOutput()
     */
    public function getOutput()
    {
        $election = $this->count->getElection();
        $method = $this->count->getMethod();

        $candidates = $election->candidates;
        $stages = $method->stages;

        $table = '';

        $table_headings = array('Candidates', 'Number of votes');

        $table_header = '<thead><tr><th rowspan="2">Candidates</th><th colspan="' . count($stages) . '">Number of votes</th></tr><tr>';
        foreach (array_keys($stages) as $stage_id) {
            $table_header .= '<th>' . sprintf('Stage %d', $stage_id) . '</th>';
        }
        $table_header .= '</tr></thead>';

        $table_rows = array();
        foreach ($candidates as $candidate) {
            $row = array();
            $row[] = htmlspecialchars($candidate->name);
            foreach ($stages as $stage_id => $stage) {
                $cell = '<div class="droophp-votes">' . number_format($stage['votes'][$candidate->cid]) . '</div>';
                if (!empty($stage['changes'][$candidate->cid])) {
                    $cell .= '<ul class="droophp-changes"><li>' . implode(
                        '</li><li>',
                        array_map('htmlspecialchars', $stage['changes'][$candidate->cid])
                    ) . '</li></ul>';
                }
                $row[] = $cell;
            }
            $table_rows[] = $row;
        }

        $table_body = '<tbody>';
        foreach ($table_rows as $row) {
            $table_body .= '<tr><th class="droophp-candidate-name">' . array_shift($row) . '</th><td>' . implode('</td><td>', $row) . '</td></tr>';
        }
        $table_body .= '</tbody>';

        $table = '<table class="droophp-output">' . $table_header . $table_body . '</table>';

        $title = sprintf('Results: %s', htmlspecialchars($election->title));

        $elected_names = array();
        foreach ($candidates as $candidate) {
            if ($candidate->state === Candidate::STATE_ELECTED) {
                $elected_names[] = $candidate->name;
            }
        }

        $output = '<h1 class="droophp-heading">' . $title . '</h1>';
        $output .= '<dl>';
        $output .= sprintf('<dt>Elected:</dt><dd>%s</dd>', htmlspecialchars(implode(', ', $elected_names)));
        $output .= sprintf('<dt>Number of candidates:</dt><dd>%s</dd>', number_format($election->num_candidates));
        $output .= sprintf('<dt>Vacancies:</dt><dd>%s</dd>', number_format($election->num_seats));
        $output .= sprintf('<dt>Valid ballots:</dt><dd>%s</dd>', number_format($election->num_valid_ballots));
        $output .= sprintf('<dt>Invalid ballots:</dt><dd>%s</dd>', number_format($election->num_invalid_ballots));
        $output .= sprintf('<dt>Quota:</dt><dd>%s</dd>', number_format($method->quota));
        $output .= sprintf('<dt>Stages:</dt><dd>%d</dd>', count($stages));
        $output .= '</dl>';
        $output .= $table;

        // Optionally, output as an HTML fragment (excluding DOCTYPE, etc).
        if ($this->config->getOption('html_fragment')) {
            return $output;
        }

        // Output a complete HTML5 page.
        $output = '<!DOCTYPE html>'
            . '<html lang="en" dir="ltr">'
            . '<head>'
            . '<meta charset="utf-8" />'
            . '<title>' . $title . '</title>'
            . '<style type="text/css">' . $this->getCss() . '</style>'
            . '</head>'
            . '<body>'
            . $output
            . '</body></html>';

        return $output;
    }

    /**
     * Get the CSS for output.
     */
    protected function getCss() {
        return 'body { font: 14px/1.4em sans-serif; }'
            . 'table { border-collapse: collapse; }'
            . 'td, th { border: 1px solid #CCC; padding: 0.5em; vertical-align: top; }'
            . 'dt { font-weight: bold; } '
            . 'ul { list-style-type: none; padding: 0; margin: 0; font-size: 0.9em; color: #777; }'
            . 'li { margin-top: 0.5em; line-height: 1.1em; }';
    }

}
