<?php
/**
 * DockBlockToReturnTest.php
 *
 * Class DockBlockToReturnTest
 *
 * @category   codingTests
 * @package    Suzunone\LaravelSchemaSpicy\Tests
 * @subpackage Suzunone\LaravelSchemaSpicy\Tests
 * @author     suzunone<suzunone.eleven@gmail.com>
 * @copyright  Project codingTests
 * @license    BSD 3-Clause License
 * @version    1.0
 * @link       https://github.com/suzunone/codingTests
 * @see        https://github.com/suzunone/codingTests
 * @since      2022/02/05
 */

namespace Suzunone\LaravelSchemaSpicy\Tests;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Query\Grammars\Grammar;
use Illuminate\Database\Query\Processors\Processor;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Suzunone\LaravelSchemaSpicy\Console\SchemaSpyXMLCommand;
use Mockery as m;
class DockBlockToReturnTest extends TestCase
{

    public function testDockBlodk()
    {
        $target_model = new \Suzunone\LaravelSchemaSpicy\Tests\App\Models\Favorite;
        $reflectionClass = new \ReflectionClass($target_model);
        $command = new SchemaSpyXMLCommand();


        foreach ($reflectionClass->getMethods() as $reflectionMethod) {
            if ($reflectionMethod->getParameters()) {
                return;
            }

            if (!$reflectionMethod->isPublic()) {
                return;
            }

            $method = $reflectionMethod->getName();
            if (
                method_exists(Model::class, $method)
                || Str::startsWith($method, 'get')
            ) {
                return;
            }

            $res = $command->getRelateByDoc($reflectionMethod, $target_model);

            $this->assertEquals(BelongsTo::class, $res, $method);
        }
    }

}
