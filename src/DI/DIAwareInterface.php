<?php
namespace Simox\DI;

interface DIAwareInterface
{
	public function setDI( $di );

	public function getDI();
}
