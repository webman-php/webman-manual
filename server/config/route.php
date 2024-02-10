<?php
/**
 * This file is part of webman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author    walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link      http://www.workerman.net/
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Webman\Route;

// doc
Route::any('/doc/{lang}', [app\controller\Doc::class,'view']);
Route::any('/doc/{lang}/', [app\controller\Doc::class,'view']);
Route::any('/doc/{lang}/{path:.+}.html', [app\controller\Doc::class,'view']);

Route::any('/doc/{path:.+}.{ext:jpg|png|gif|jpeg}', [app\controller\Doc::class,'file']);
