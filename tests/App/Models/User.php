<?php
/**
 * ExampleModel.php
 *
 * Class ExampleModel
 *
 * @category   codingTests
 * @package    Suzunone\LaravelSchemaSpicy\Tests\data
 * @subpackage Suzunone\LaravelSchemaSpicy\Tests\data
 * @author     suzunone<suzunone.eleven@gmail.com>
 * @copyright  Project codingTests
 * @license    BSD 3-Clause License
 * @version    1.0
 * @link       https://github.com/suzunone/codingTests
 * @see        https://github.com/suzunone/codingTests
 * @since      2022/02/05
 */

namespace Suzunone\LaravelSchemaSpicy\Tests\App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Model
{
    public function BelongsToHint():BelongsTo
    {
        return $this->belongsTo();
    }

}
