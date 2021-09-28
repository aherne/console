<?php
namespace Lucinda\Console;

use Lucinda\Console\Compilers\DivCompiler;
use Lucinda\Console\Compilers\TableCompiler;
use Lucinda\Console\Compilers\ListCompiler;

/**
 * Parses a pseudo-HTML and builds a console text ready for display
 */
class Wrapper
{
    private $body;
    private $isWindows;

    /**
     * Parses pseudo-HTML received, taking into account if platform has styling abilities
     *
     * @param string $body
     */
    public function __construct(string $body)
    {
        $this->isWindows = stripos(php_uname("s"), "win")!==false;

        $this->setBody($body);
    }

    /**
     * Sets body of text to be displayed
     *
     * @param string $body Body containing pseudo-html
     * @throws Exception If an error prevents proceding any further
     */
    private function setBody(string $body): void
    {
        $divCompiler = new DivCompiler($body, $this->isWindows);
        $body = $divCompiler->getBody();
        
        $tableCompiler = new TableCompiler($body, $this->isWindows);
        $body = $tableCompiler->getBody();
        
        $listCompiler = new ListCompiler($body, $this->isWindows);
        $body = $listCompiler->getBody();
        
        $this->body = $body;
    }

    /**
     * Gets payload to be displayed back to users
     *
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
