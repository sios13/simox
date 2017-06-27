<?php
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    // ...

    public function testAddRoute()
    {
        // Arrange
        $router = new Simox\Router();

        $router->setDi( new Simox\DI() );

        // Act
        $router->addRoute( "asd", "asd#asd" );

        // Assert
        $this->assertEquals( "asd", $router->reverseRoute("asd", "asd") );

        /*
        // Arrange
        $a = new Money(1);

        // Act
        $b = $a->negate();

        // Assert
        $this->assertEquals(-1, $b->getAmount());
        */
    }
}
