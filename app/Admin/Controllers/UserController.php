<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

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
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        $grid->column('email_verified_at', __('Email verified at'));
        $grid->column('remember_token', __('Remember token'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        $grid->filter(function ($filter) {
            // Remove the default id filter
            $filter->disableIdFilter();
            // Add a column filter
            $filter->like('name', 'Name');
            $filter->like('email', 'Email');
            // Sets the range query for the created_at field
            $filter->between('created_at', 'Created At')->datetime();
        });

        $grid->export(function ($export) {
            $export->column('email_verified_at', function ($value, $original) {
                return Carbon::parse($value)->format('Y/m/d');
            });
            $export->column('created_at', function ($value, $original) {
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
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        $show->field('email_verified_at', __('Email verified at'));
        $show->field('password', __('Password'));
        $show->field('remember_token', __('Remember token'));
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
        $form = new Form(new User());

        $form->text('name', __('Name'))->rules('required');
        $form->email('email', __('Email'))->rules('required');
        $form->datetime('email_verified_at', __('Email verified at'))->default(date('Y-m-d H:i:s'));

        $form->password('password', __('Password'))->rules('required')->default(function ($form) {
            return $form->model()->password;
        });
        $form->confirm('confirm edit ?', 'edit');
        $form->confirm('confirm create ?', 'create');
        return $form;
    }
}
