<?php


namespace Kcloze\Bot;

use GuzzleHttp\Client;
use Medoo\Medoo;

class Utils
{
    private static $instance = null;

    private static $guzzle = null;

    private static $db = null;

    private $ddl = <<<DDL
    CREATE TABLE docs
(
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    func CHAR,
    content TEXT
);
CREATE UNIQUE INDEX docs_func_uindex ON docs (func);
DDL;

    private function __construct()
    {
        self::$guzzle = new Client();
        self::$db     = new Medoo([
            'database_type' => 'sqlite',
            'database_file' => BOT_ROOT . '/log/data.db'
        ]);

        self::$db->query($this->ddl);
    }

    public static function getInstance(): Utils
    {
        if (self::$instance === null)
            self::$instance = new self();

        return self::$instance;
    }

    public function phpdoc($func)
    {
        $docs = self::$db->select('docs', '*', ['func' => $func]);

        if (empty($docs)) {
            $uri     = sprintf('http://php.net/manual/zh/function.%s.php', $func);
            $content = self::$guzzle->request('GET', $uri)->getBody()->getContents();

            preg_match('#<p class="verinfo">(.*?)</p>#', $content, $verinfo);
            preg_match('#<span class="dc-title">(.*?)</span>#', $content, $dc);

            $docs = sprintf('%s - %s - %s - %s', $func, $verinfo[1], $dc[1], $uri);

            self::$db->insert('docs', ['func' => $func, 'content' => $docs]);
        } else {
            $docs = $docs[0]['content'];
        }

        return $docs;
    }

}
