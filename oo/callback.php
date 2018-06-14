<?php require '../_config.php'; 

# Fun��o callback
function my_callback_function()
{
    echo "hello world@<br/>";
}

# M�todo callback
class MyClass
{
    public static function myCallBackMethod()
    {
        echo "Hello World!";
    }
}

# primeiro callback: simples
call_user_func('my_callback_function');                                         // Display 'hello world@'

# segundo callback: chamada � m�todos est�ticos
call_user_func(array('MyClass', 'myCallBackMethod'));                           // Display 'Hello World!'

# terceiro callback: chamada � m�todos de objetos
$obj = new MyClass();
call_user_func(array($obj, 'myCallBackMethod'));                                // Display 'Hello World!'

# quarto callback: Chamada � m�todos est�ticos (a partir do PHP 5.2.3)
call_user_func('MyClass::myCallBackMethod');                                    // Display 'Hello World!'

# quinto callback: Chamada relativa � m�todos est�ticos (a partir do PHP 5.3.0)
class A
{
    public static function who()
    {
        echo "A\n";
    }
}

class B extends A
{
    public static function who()
    {
        echo "B\n";
    }
}

call_user_func(array('B', 'who'        ));                                      // Display 'B'
call_user_func(array('B', 'parent::who'));                                      // Display 'A'

?>