<?php

namespace Encore\Admin\Controllers;

use App\Models\Post;
use App\Models\User;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Encore\Admin\Form;

class AdminController extends Controller
{
    use HasResourceActions;

    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Title';

    /**
     * Set description for following 4 action pages.
     *
     * @var array
     */
    protected $description = [
        //        'index'  => 'Index',
        //        'show'   => 'Show',
        //        'edit'   => 'Edit',
        //        'create' => 'Create',
    ];

    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return $this->title;
    }

    /**
     * Index interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function index(Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['index'] ?? trans('admin.list'))
            ->body($this->grid());
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['show'] ?? trans('admin.show'))
            ->body($this->detail($id));
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['edit'] ?? trans('admin.edit'))
            ->body($this->form()->edit($id));
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Request $request, Content $content)
    {
        return $content
            ->title($this->title())
            ->description($this->description['create'] ?? trans('admin.create'))
            ->body($this->form());

    }

    public function confirm_update(Request $request, Content $content)
    {
        $data = $request->all();
        if (str_contains($request->server()['REQUEST_URI'], 'users-clients')) {
            $form = new Form(new User());
            $form->text('name')->readonly()->value($data['name']);
            $form->email('email')->readonly()->value($data['email']);
            $form->datetime('email_verified_at')->readonly()->value($data['email_verified_at']);
            $form->password('password')->readonly()->value($data['password'])->default(function ($form) {
                return $form->model()->password;
            })->readonly();
            $form->text('remember_token')->value($data['remember_token']);
            $form->builder()->setMode('confirm_update');
            return $content
                ->title($this->title())
                ->description($this->description['confirm'] ?? trans('admin.confirm'))
                ->body($form);
        } elseif (str_contains($request->server()['REQUEST_URI'], 'posts')) {
            $form = new Form(new Post());
            $form->text('name')->readonly()->value($data['name']);
            $form->textarea('description')->readonly()->value($data['description']);
            $form->select('created user')->disable()->value($data['user_id'])->options(User::all()->pluck('name', 'id'), __('Created User'));
            $form->builder()->setMode('confirm_update');
            return $content
                ->title($this->title())
                ->description($this->description['confirm_update'] ?? trans('admin.confirm_update'))
                ->body($form);
        }

    }
}
