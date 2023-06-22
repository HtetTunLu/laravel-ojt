<?php

namespace App\Admin\Controllers;

use App\Models\AdminRoleUser;
use App\Models\Post;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

use App\Admin\Extensions\Tools\ImportButton; // Add for custom CSV Import Button
use Encore\Admin\Layout\Content; // Add for CSV Import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        Admin::script('$(document).ready(function(){
            $(".column-__actions__").click(function(e){
                $("<div>", {
                    "class": "custom-name",
                    "style": "width: 100%;",
                }).appendTo(".swal2-content");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0; width: 40%; display: inline-block;",
                    text: "Name: "
                }).appendTo(".custom-name");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0",
                    text: $(this).parent()[0].children[2].textContent
                }).appendTo(".custom-name");

                $("<div>", {
                    "class": "custom-description",
                    "style": "width: 100%;",
                }).appendTo(".swal2-content");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0; width: 40%; display: inline-block;",
                    text: "Description: "
                }).appendTo(".custom-description");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0",
                    text: $(this).parent()[0].children[3].textContent
                }).appendTo(".custom-description");

                $("<div>", {
                    "class": "custom-user",
                    "style": "width: 100%;",
                }).appendTo(".swal2-content");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0; width: 40%; display: inline-block;",
                    text: "Created User: "
                }).appendTo(".custom-user");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0",
                    text: $(this).parent()[0].children[4].textContent
                }).appendTo(".custom-user");

                $("<div>", {
                    "class": "custom-status",
                    "style": "width: 100%;",
                }).appendTo(".swal2-content");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0; width: 40%; display: inline-block;",
                    text: "Status: "
                }).appendTo(".custom-status");

                $("<span>", {
                    "style": "font-size: 25px; text-align: left; margin: 15px 0",
                    text: $(this).parent()[0].children[5].textContent
                }).appendTo(".custom-status");
            });

            $(".dropdown-menu li a").click(function(data){

                if($(this)[0].textContent === "Delete") {
                    $(".swal2-container")[0].children[0].style.width = "400px";

                }
            });
        });');

        // dd(Post::where('admin_user_id', 1));
        $grid = new Grid(new Post());
        if (AdminRoleUser::where('user_id', Auth::user()->id)->first()->role_id !== 1) {
            $grid->model()->where('admin_user_id', Auth::user()->id);
        }
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
        $grid->column('admin_user.name', __('Created User'))
            ->setAttributes(['style' => 'color:green;'])
            ->help('This column is Created User name column');
        $grid->column('status')->display(function ($status) {
            return $status === 1 ? 'on' : 'off';
        });
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
            $export->originalValue(['name', 'user_id', 'status']);
            $export->column('created_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
            $export->column('updated_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
        });

        $grid->actions(function ($value) {
            $value->row;

            $value->getKey();
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
        $show->field('admin_user.name', __('Created User'));
        $show->field('status', __('Status'))->as(function ($status) {
            return $status === 1 ? 'ON' : 'OFF';
        });
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
            $(".pull-right button").prop("class", "btn btn-primary submit");
            $(".submit").click(function(){
                if($(".submit").text() === "Submit") {
                    $errArr = [];
                    $dataArr = ["name", "description"]
                    $.each($dataArr, function(index, data){
                        if($(`.error-msg-${data}`)[0]) {
                            $(`.error-msg-${data}`).remove();
                        }
                    })
                    $.each($dataArr, function(index, value) {
                        if($(`.${value}`)[0].value === "") {
                            $errArr.push(value);
                            $("<div>", {
                                "class": `error-msg-${value}`,
                                "style": "width: 100%; color: red;",
                                "text": `${index === 0 ? "Name" : "Description"} cannot be blank!`
                            }).appendTo(index === 0 ? $(`.${value}`)[0].parentNode.parentNode : $(`.${value}`)[0].parentNode);
                        }
                    })
                    if($errArr.length === 0) {
                        $("form input").prop("readonly", true);
                        $("form textarea").prop("readonly", true);
                        $(".bootstrap-switch")[0].style.pointerEvents = "none";
                        $(".select2-selection").click(function() {
                            $(".select2-dropdown")[0].style.visibility = "hidden";
                        });
                        $(".submit").html("Confirm");
                        $(".btn-warning").html("Back");
                    }
                    return false;

                }
            });

            $(".btn-warning").click(function(){
                if($(".btn-warning").text() === "Back") {
                    $("form input").prop("readonly", false);
                    $("form textarea").prop("readonly", false);
                    $(".bootstrap-switch")[0].style.pointerEvents = "auto";
                    $(".select2-selection").click(function() {
                        $(".select2-dropdown")[0].style.visibility = "visible";
                    });
                    $(".submit").html("Submit");
                    $(".btn-warning").html("Reset");
                    return false;
                }
            });
        });');

        $form = new Form(new Post());
        $form->text('name', __('Name'))->rules('required');
        $form->textarea('description', __('Description'))->rules('required');
        $form->switch('status', __("Status"))->default(1);
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
                $user_id = Auth::user()->id;
                $status = (Int) $row[4];
                $post = Post::where('id', $id)->first();
                if (!$post) {
                    $req = new Post();
                    $req->id = $id;
                    $req->name = $name;
                    $req->description = $description;
                    $req->admin_user_id = $user_id;
                    $req->status = $status;
                    $req->save();
                } else {
                    $post->name = $name;
                    $post->description = $description;
                    $post->admin_user_id = $user_id;
                    $post->status = $status;
                    $post->save();
                }
            }

        }
        return redirect('admin/posts');
    }
}
