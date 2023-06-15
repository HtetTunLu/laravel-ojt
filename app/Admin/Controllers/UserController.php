<?php

namespace App\Admin\Controllers;

use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Illuminate\Support\Facades\Hash;

class UserController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'User';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        Admin::script('$(document).ready(function(){
            $(".column-__actions__").click(function(e){
                console.log($(this).parent());
                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Username: " +$(this).parent()[0].children[2].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Name: " + $(this).parent()[0].children[3].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Email: " + $(this).parent()[0].children[4].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Phone Number: " + $(this).parent()[0].children[5].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Date of Birth: " + $(this).parent()[0].children[6].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Address: " + $(this).parent()[0].children[7].textContent
                }).appendTo(".swal2-content");

                $("<div>", {
                    "class": "new",
                    "style": "font-size: 30px; text-align: center; margin: 15px 0;",
                    text: "Role: " + $(this).parent()[0].children[8].textContent
                }).appendTo(".swal2-content");
            });

            $(".dropdown-menu li a").click(function(data){

                if($(this)[0].textContent === "Delete") {

                    console.log($(".swal2-container").parent());
                    $(".swal2-container")[0].children[0].style.width = "600px";

                }
            });
        });');
        $userModel = config('admin.database.users_model');

        $grid = new Grid(new $userModel());

        $grid->column('id', 'ID')->sortable();
        $grid->column('username', trans('admin.username'));
        $grid->column('name', trans('admin.name'));
        $grid->column('email', __('Email'));
        $grid->column('phone', __('Phone Number'));
        $grid->column('dob', __('Date of Birth'))->display(function ($value) {
            return Carbon::parse($value)->format('Y/m/d');
        });
        $grid->column('address', __('Address'));
        $grid->column('roles', trans('admin.roles'))->pluck('name')->label();
        $grid->column('created_at', trans('admin.created_at'));
        $grid->column('updated_at', trans('admin.updated_at'));

        $grid->actions(function (Grid\Displayers\Actions $actions) {
            if ($actions->getKey() == 1) {
                $actions->disableDelete();
            }
        });

        $grid->tools(function (Grid\Tools $tools) {
            $tools->batch(function (Grid\Tools\BatchActions $actions) {
                $actions->disableDelete();
            });
        });

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->like('username', 'Username');
            $filter->like('name', 'Name');
            $filter->like('email', 'Email');
            // Sets the range query for the created_at field
            $filter->between('created_at', 'Created At')->datetime();
        });

        $grid->export(function ($export) {
            $export->column('roles', function ($value) {
                $roleModel = config('admin.database.roles_model');
                $roleName = $roleModel::all()->pluck('name');
                return trim($roleName, '"[]"');
            });
            // $export->originalValue(['roles'] ,function($value, $original) {
            //     return $value;
            // });
            $export->column('dob', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
            $export->column('updated_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
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
        $userModel = config('admin.database.users_model');

        $show = new Show($userModel::findOrFail($id));

        $show->field('id', 'ID');
        $show->field('username', trans('admin.username'));
        $show->field('name', trans('admin.name'));
        $show->field('roles', trans('admin.roles'))->as(function ($roles) {
            return $roles->pluck('name');
        })->label();
        $show->field('permissions', trans('admin.permissions'))->as(function ($permission) {
            return $permission->pluck('name');
        })->label();
        $show->field('created_at', trans('admin.created_at'));
        $show->field('updated_at', trans('admin.updated_at'));

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
                    $("form input").prop("readonly", true);
                    $("form textarea").prop("readonly", true);
                    $(".btn-file")[0].style.visibility = "hidden";
                    $(".submit").html("Confirm");
                    $(".btn-warning").html("Back");
                    return false;
                }
            });

            $(".btn-warning").click(function(){
                if($(".btn-warning").text() === "Back") {
                    $("form input").prop("readonly", false);
                    $("form textarea").prop("readonly", false);
                    $(".btn-file")[0].style.visibility = "visible";
                    $(".submit").html("Submit");
                    $(".btn-warning").html("Reset");
                    return false;
                }
            });
        });');
        $userModel = config('admin.database.users_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->email('email', __('Email'))->rules('required');
        $form->datetime('dob', __('Date of Birth'))->rules('required');
        $form->text('phone', __('Phone Number'))->rules('required');
        $form->textarea('address', __('Address'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));
        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });
        $form->disableCreatingCheck();
        $form->disableEditingCheck();
        $form->disableViewCheck();

        return $form;
    }
}
