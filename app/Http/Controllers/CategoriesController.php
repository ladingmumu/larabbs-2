<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Topic;
use App\Models\Category;
use App\Models\Link;
use App\Models\User;

class CategoriesController extends Controller
{
    public function show(Category $category, Request $request, Topic $topic, Link $link)
    {
        $topics = $topic->withOrder($request->order)
                        ->where('category_id', $category->id)
                        ->paginate(20);
        // 活跃用户列表
        $user = new User();
        $active_users = $user->getActiveUsers();
        // 资源链接
        $links = $link->getAllCached();

        // 传参变量话题和分类到模板中
        return view('topics.index', compact('topics', 'category', 'active_users', 'links'));
    }
}
