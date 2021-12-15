<?php

/*
SRP
ПРИНЦИП ЕДИНСТВЕННОЙ ОТВЕТСТВЕННОСТИ (КАЖДЫЙ КЛАСС ОТВЕЧАЕТ ЗА ОДНО)
*/

class PostsConverter
{
    private $post;

    public function __construct($post)
    {
        $this->post = $post;
    }

    public function toJSON()
    {
        return json_encode([
            $this->post->getTitle(),
            $this->post->getContent()
        ]);
    }

    public function toXML()
    {
        return '
        <post>
            <title>' . $this->post->getTitle() . '</title>
            <content>' . $this->post->getContent() . '</content>
        </post>';
    }
}

class Posts
{
    private $title;

    private $content;

    public function __construct($title, $content)
    {
        $this->title = $title;

        $this->content = $content;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }
}

$post = new Posts("Hello world!", "This is my first posts!");

$post_converter = new PostsConverter($post);

//var_dump( $post_converter->toJSON() );
//var_dump( $post_converter->toXML() );

/* -------------------------------------------------------------------------- */

/*
 * ORP - Open Closed Principle
 * ПРИНЦИП ОТКРЫТОСТИ И ЗАКРЫТОСТИ
 * К примеру есть класс который надо расширить но нон закрыт для изменения,
 * т.е мы можем их расширять с помощью интерфейсов, наследования
 * Но не менять их исходный код
 */

class Dev
{
    public $type;

    public $cost;

    public function __construct($cost)
    {
        $this->type = 'dev';

        $this->cost = $cost;
    }
}

class Manager
{
    public $type;

    public $cost;

    public function __construct($cost)
    {
        $this->type = 'manager';

        $this->cost = $cost;
    }
}

class ProjectManager
{
    public $type;

    public $cost;

    public function __construct($cost)
    {
        $this->type = 'project manager';

        $this->cost = $cost;
    }
}

class CostsCalculator
{
    public $workers = [];

    public function __construct($workers)
    {
        $this->workers = $workers;
    }

    public function sum()
    {
        $all_cost = 0;

        foreach ( $this->workers as $worker ) {
            if ( $worker->type == 'dev' ) {
                $all_cost += $worker->cost * 5;
            } else if ( $worker->type == 'manager' ) {
                $all_cost += $worker->cost * 7;
            } else if ( $worker->type == 'project manager' ) {
                $all_cost += $worker->cost * 9;
            }
        }

        return $all_cost;
    }
}

$calculator = new CostsCalculator([
    new Dev(40000),
    new Manager(700000),
    new ProjectManager(9999999),
]);

//echo $calculator->sum(); //95099991

/*
 * Это плохой пример, потому что в нашей конструкции много проверок, много повторяющегося кода
 * Если к примеру нам нужно будет добавить новую должность к примеру уборщик, то надо лезть в класс
 * то тут на помощь приходить ORP
 */

/* -------------------------------------------------------------------------- */
/*                                 Рефакторинг                                */
/* -------------------------------------------------------------------------- */

interface Worker
{
    public function make_sum();
}

class DevRefactoring implements Worker
{
    public $cost;

    public function __construct($cost)
    {
        $this->cost = $cost;
    }

    public function make_sum()
    {
        return $this->cost * 5;
    }
}

class ManagerRefactoring implements Worker
{
    public $cost;

    public function __construct($cost)
    {
        $this->cost = $cost;
    }

    public function make_sum()
    {
        return $this->cost * 7;
    }
}

class ProjectManagerRefactoring implements Worker
{
    public $cost;

    public function __construct($cost)
    {
        $this->cost = $cost;
    }

    public function make_sum()
    {
        return $this->cost * 9;
    }
}

class CostsCalculatorRefactoring
{
    public $workers = [];

    public function __construct($workers)
    {
        $this->workers = $workers;
    }

    public function sum()
    {
        $all_cost = 0;

        foreach ( $this->workers as $worker ) {
            $all_cost += $worker->make_sum();
        }

        return $all_cost;
    }
}

$calculator = new CostsCalculatorRefactoring([
    new DevRefactoring(40000),
    new ManagerRefactoring(700000),
    new ProjectManagerRefactoring(9999999),
]);

//echo $calculator->sum(); // 95099991

/*
 * С помощью интерфейса мы расширили функционал.
 * Отпала нужда в переменной $type.
 * Теперь подсчёты у нас делаются в самом классе работника и именно вот так можно расширять функционал классов
 * не меняя их исходного кода.
 * Это вся суть ORP мы можем расширять классы но не изменять их исходного кода.
 */

/* -------------------------------------------------------------------------- */

/*
 * LSP - Liskov Substitution principle
 * Принцип подстановки Барбары Лисков
 * Функции, которые используют базовый тип
 * должны иметь возможнсть использовать подтипы базовошо типа,
 * не зная об этом (?)
 */

class User
{
    public function access()
    {
        echo "You have access";
    }
}

class SuperUser extends User
{
    public function access()
    {
        echo "You have access!";
    }
}

class Moderator extends SuperUser
{
    // Some functions
}

class Admin extends SuperUser
{
    // Some functions
}

class Guest extends User
{
    public function access()
    {
        throw new Exception('You can\'t have access');
    }
}

function getSystemAccess( $user )
{
    $user->access();
}

getSystemAccess( new Admin() );
getSystemAccess( new Moderator() );
getSystemAccess( new Guest() ); // Удалить

/*
 * У нас отдельный класс, который унаследовал просто пользователь
 * Но он даёт отдельный доступ нашему классу Модератору и Админу
 * Так как класс Гость наследован от просто пользователя, т.е не имеет доступа.
 * Модератор и Админ не знают что они просто пользователи, они знают что они Супер пользователи
 */

/* -------------------------------------------------------------------------- */

/*
 * ISP - Interface segregation principle
 * Принцип разделения интерфейсов
 * Позволяет делать систему более гибкой, при внесении каких-то изменений.
 * И суть в том чтобы брать и разделять "толстые интрфейсы" на болие тонкие.
 */

interface Shape
{
    public function area();
}

interface FullShape
{
    public function volume();
}

class Pyramid implements Shape, FullShape
{
    public function area()
    {
        // TODO: Implement area() method.
    }

    public function volume()
    {
        // TODO: Implement volume() method.
    }
}

class Rect implements Shape
{
    public function area()
    {
        // TODO: Implement area() method.
    }
}

/*
 * Мы разделили интрфейс Shape на два
 * Т.е добавили новый интерфейс FullShape, который себя включает функцию volume(),
 * до этого эта функция была в интерфейсе shape
 * В этом и заключается этот метод, разделение более толстых интерфейсов на более тонкие.
 */

/* -------------------------------------------------------------------------- */