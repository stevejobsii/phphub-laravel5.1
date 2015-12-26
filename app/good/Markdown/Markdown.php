<?php namespace App\good\Markdown;

use League\HTMLToMarkdown\HtmlConverter;
use App\good\parsedown\parsedown\Parsedown;
use Purifier;
use App\good\parsedown\ParsedownExtra\ParsedownExtra;

class Markdown
{
    protected $htmlParser;
    protected $markdownParser;

    public function __construct()
    {
        $this->htmlParser = new HtmlConverter();
        $this->htmlParser->getConfig()->setOption('header_style', 'setext');

        $this->markdownParser = new Parsedown;
    }

    public function convertHtmlToMarkdown($html)
    {
        return $this->htmlParser->convert($html);
    }

    public function convertMarkdownToHtml($markdown)
    {
        $convertedHmtl = $this->markdownParser->text($markdown);
        return Purifier::clean($convertedHmtl, 'user_topic_body');
    }
}
