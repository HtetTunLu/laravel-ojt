<?php

namespace App\Admin\Controllers;

use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Admin\Extensions\Tools\ImportButton; // Add for custom CSV Import Button
use Encore\Admin\Layout\Content; // Add for CSV Import
use Illuminate\Http\Request;

class PostController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Post';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Post());

        // Use custom button tools here which made above.
        $grid->tools(function ($tools) {
            $tools->append(new ImportButton());
        });

        $grid->column('id', __('Id'));
        // $grid->column('name', __('Name'));
        $grid->column('name')->display(function ($title) {
            return "<span style='color:blue'>$title</span>";
        });

        $grid->column('description', __('Description'));
        $grid->column('user.name', __('Created User'))
            ->setAttributes(['style' => 'color:green;'])
            ->help('This column is Created User name column');
        ;
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));
        $grid->paginate(10);

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->like('name', 'name');
            // Add a column filter
            $filter->like('description', 'description');
            // Sets the range query for the created_at field
            $filter->between('created_at', 'Created At')->datetime();
        });
        $grid->quickSearch(function ($model, $query) {
            $model->where('name', 'like', "%{$query}%")
                ->orWhere('description', 'like', "%{$query}%");
        });

        $grid->export(function ($export) {
            $export->originalValue(['name', 'user_id']);
            $export->column('created_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
            $export->column('updated_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
        });

        $grid->actions(function ($value) {
            $value->getKey();
            // dd($value);
        });
        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Post::findOrFail($id));
        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('description', __('Description'));
        $show->field('user.name', __('Created User'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::script('$(document).ready(function(){
            $(".btn-primary").click(function(){
                if($(".btn-primary").text() === "Submit") {
                    $("form input").prop("readonly", true);
                    $("form textarea").prop("readonly", true);
                    $(".select2-selection").click(function() {
                        console.log("clicked");
                        $(".select2-dropdown")[0].style.visibility = "hidden";
                    });
                    $(".btn-primary").html("Confirm");
                    $(".btn-warning").html("Back");
                    return false;
                }
            });

            $(".btn-warning").click(function(){
                if($(".btn-warning").text() === "Back") {
                    $("form input").prop("readonly", false);
                    $("form textarea").prop("readonly", false);
                    $(".select2-selection").click(function() {
                        $(".select2-dropdown")[0].style.visibility = "visible";
                    });
                    $(".btn-primary").html("Submit");
                    $(".btn-warning").html("Reset");
                    return false;
                }
            });
        });');

        $form = new Form(new Post());
        $form->text('name', __('Name'))->rules('required');
        $form->textarea('description', __('Description'))->rules('required');
        $form->select('user_id')->options(User::all()->pluck('name', 'id'))->rules('required');
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();
        return $form;
    }

    /**
     * Import interface.
     */
    protected function import(Content $content, Request $request)
    {
        $file = $request->file('file');
        $csv = array_map('str_getcsv', file($file));

        foreach ($csv as $key => $row) {
            // index 0 for titles
            if ($key > 0) {
                $id = (Int) $row[0];
                $name = $row[1];
                $description = $row[2];

                $post = Post::where('id', $id)->first();
                if (!$post) {
                    $req = new Post();
                    $req->id = $id;
                    $req->name = $name;
                    $req->description = $description;
                    $req->save();
                } else {
                    $post->name = $name;
                    $post->description = $description;
                    $post->save();
                }
            }

        }
        return redirect('admin/posts');
    }
}
