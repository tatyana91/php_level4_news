<?php
/**
 *	class NewsDB
 *	Содержит основные методы для работы с новостной лентой
 *  Реализует интерфейс INewsDB, IteratorAggregate
 */
class NewsDB implements INewsDB, IteratorAggregate{
    private const DB_NAME = '../news.db';
	private const RSS_NAME = 'rss.xml';
	private const RSS_TITLE = 'Последние новости';
	private const RSS_LINK = 'http://php3.loc/news/news.php';
	private $_categories;
	private $_db;

    /**
     * Создать объект класса DB для работы с базой данных
     * Заполнить свойства $_categories категориями из базы данных
     *
     * @return void
     */
	function __construct(){
	    $dsn_params = array();
        $dsn_params['type'] = 'sqlite';
        $dsn_params['name'] = self::DB_NAME;
	    $this->_db = new DB($dsn_params);
        $this->getCategories();
	}

    /**
     * Сохранить новость и перегенерировать ленту rss
     *
     * @param string $title - название новости
     * @param int $category - категория новости
     * @param string $description - описание новости
     * @param string $text - текст новости
     * @param string $source - ссылка на источник новости
     *
     * @return bool булев результат (успех/ошибка)
     *
     */
	function saveNews($title, $category, $description, $text, $source){
		$sql = "INSERT INTO msgs (title, category, description, text, source, datetime) 
                VALUES (:title, :category, :description, :text, :source, :datetime)";
		$datetime = time();
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':title', $title, PDO::PARAM_STR);
		$stmt->bindParam(':category', $category, PDO::PARAM_INT);
		$stmt->bindParam(':description', $description, PDO::PARAM_STR);
		$stmt->bindParam(':text', $text, PDO::PARAM_STR);
		$stmt->bindParam(':source', $source, PDO::PARAM_STR);
		$stmt->bindParam(':datetime', $datetime, PDO::PARAM_INT);
		if (!$stmt->execute()) {
			return false;
		}

		$this->createRss();
		return true;
	}

    /**
     * Получить список новостей
     *
     * @return array $result список новостей
     */
	function getNews(){
        $sql = "SELECT 
                        msgs.id as id, 
                        title, 
                        category.name as category, 
                        description,
                        text,
                        source, 
                        datetime 
                    FROM msgs, category 
                    WHERE category.id = msgs.category 
                    ORDER BY msgs.id DESC";
        $items = $this->_db->query($sql);
        return $this->_db->db2Arr($items);
	}

    /**
     * Получить новость с запрашиваемым id
     *
     * @param int $id - идентификатор новости
     *
     * @return array результирующий набор в виде ассоциативного массива
     *
     */
	function getNewsItem($id){
        $sql = "SELECT
                title,
                category.name as category,
                text,
                source,
                datetime
            FROM msgs, category
            WHERE
                category.id = msgs.category
                AND msgs.id = :id";
        $stmt = $this->_db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $item = $stmt->execute();
        if (!$item){
            return false;
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
	}

    /**
     * Удалить новость с заданным id
     *
     * @param int $id - идентификатор новости
     *
     * @return bool булев результат (успех/ошибка)
     *
     */
	function deleteNews($id){
		$sql = "DELETE FROM msgs WHERE id = :id";
		$stmt = $this->_db->prepare($sql);
		$stmt->bindParam(':id', $id, PDO::PARAM_INT);
		if (!$stmt->execute()) {
			return false;
		}

        $this->createRss();
		return true;
	}

    /**
     * Записать список категорий в свойство $this->_categories
     *
     * @return void
     */
	function getCategories(){
		$sql = "SELECT id, name
				FROM category				
				ORDER BY name ASC";
		$items = $this->_db->query($sql);
		while ($item = $items->fetch(PDO::FETCH_ASSOC)){
		    $this->_categories[$item['id']] = $item['name'];
        }
	}

    /**
     * Реализация метода getIterator интерфейса IteratorAggregate     *
     *
     * @return ArrayIterator $result - объект класса ArrayIterator со списком категорий
     */
	function getIterator()
    {
        return new ArrayIterator($this->_categories);
    }

    /**
     * Создать rss ленту
     *
     * @return void
     */
    function createRss(){
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $dom->preserveWhiteSpace = false;

        $rss = $dom->createElement('rss');
        $version = $dom->createAttribute("version");
        $version->value = '2.0';
        $rss->appendChild($version);

        $channel = $dom->createElement('channel');

        $title = $dom->createElement('title', self::RSS_TITLE);
        $link = $dom->createElement('link', self::RSS_LINK);

        $channel->appendChild($title);
        $channel->appendChild($link);

        $news_items = $this->getNews();
        foreach($news_items as $news_item){
            $item = $dom->createElement('item');

            $title = $dom->createElement('title', $news_item['title']);
            $link = $dom->createElement('link', htmlentities($news_item['source']));

            $description = $dom->createElement('description');
            $cdata = $dom->createCDATASection($news_item['description']);
            $description->appendChild($cdata);

            $text = $dom->createElement('text');
            $cdata = $dom->createCDATASection($news_item['text']);
            $text->appendChild($cdata);

            $pubDate = $dom->createElement('pubDate', date('d.m.Y H:i', $news_item['datetime']));
            $category = $dom->createElement('category', $news_item['category']);

            $item->appendChild($title);
            $item->appendChild($link);
            $item->appendChild($description);
            $item->appendChild($text);
            $item->appendChild($pubDate);
            $item->appendChild($category);

            $channel->appendChild($item);
        }

        $dom->appendChild($channel);
        $dom->save(self::RSS_NAME);
    }
}