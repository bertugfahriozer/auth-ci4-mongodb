<?php

namespace Modules\Backend\Controllers;

use JasonGrimes\Paginator;
use Modules\Backend\Libraries\CommonTagsLibrary;
use Modules\Backend\Models\AjaxModel;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\Regex;
use CodeIgniter\API\ResponseTrait;

class Blog extends BaseController
{
    use ResponseTrait;
    private $commonTagsLib;
    private $model;

    public function __construct()
    {
        $this->model = new AjaxModel();
        $this->commonTagsLib = new CommonTagsLibrary();
    }

    public function index()
    {
        $totalItems = $this->commonModel->count('categories', []);
        $itemsPerPage = 20;
        $currentPage = $this->request->uri->getSegment(3, 1);
        $urlPattern = '/backend/blogs/(:num)';
        $paginator = new Paginator($totalItems, $itemsPerPage, $currentPage, $urlPattern);
        $paginator->setMaxPagesToShow(5);
        $bpk = ($this->request->uri->getSegment(3, 1) - 1) * $itemsPerPage;
        $this->defData = array_merge($this->defData, ['paginator' => $paginator, 'blogs' => $this->commonModel->getList('blog', [], ['limit' => $itemsPerPage, 'skip' => $bpk])]);
        return view('Modules\Backend\Views\blog\list', $this->defData);
    }

    public function new()
    {
        $this->defData['categories'] = $this->commonModel->getList('categories');
        $this->defData['authors'] = $this->commonModel->getList('users',['status'=>'active']);
        return view('Modules\Backend\Views\blog\create', $this->defData);
    }

    public function create()
    {
        $valData = ([
            'title' => ['label' => 'Sayfa Başlığı', 'rules' => 'required'],
            'seflink' => ['label' => 'Sayfa URL', 'rules' => 'required'],
            'content' => ['label' => 'İçerik', 'rules' => 'required'],
            'isActive' => ['label' => 'Yayın veya taslak', 'rules' => 'required'],
            'categories' => ['label' => 'Kategoriler', 'rules' => 'required'],
            'author' => ['label' => 'Yazar', 'rules' => 'required'],
            'created_at' => ['label' => 'Oluşturulma Tarihi', 'rules' => 'required|valid_date[d.m.Y H:i:s]']
        ]);
        if (!empty($this->request->getPost('pageimg'))) {
            $valData['pageimg'] = ['label' => 'Görsel URL', 'rules' => 'required|valid_url'];
            $valData['pageIMGWidth'] = ['label' => 'Görsel Genişliği', 'rules' => 'required|is_natural_no_zero'];
            $valData['pageIMGHeight'] = ['label' => 'Görsel Yüksekliği', 'rules' => 'required|is_natural_no_zero'];
        }
        if (!empty($this->request->getPost('description'))) $valData['description'] = ['label' => 'Seo Açıklaması', 'rules' => 'required'];
        if (!empty($this->request->getPost('keywords'))) $valData['keywords'] = ['label' => 'Seo Anahtar Kelimeleri', 'rules' => 'required'];
        if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        if ($this->commonModel->get_where(['seflink' => $this->request->getPost('seflink')], 'blog') === 1) return redirect()->back()->withInput()->with('error', 'Blog seflink adresi daha önce kullanılmış. lütfen kontrol ederek bir daha oluşturmayı deneyeyiniz.');

        $data = ['title' => $this->request->getPost('title'), 'content' => $this->request->getPost('content'), 'isActive' => (bool)$this->request->getPost('isActive'), 'seflink' => $this->request->getPost('seflink'), 'inMenu' => false, 'categories' => $this->request->getPost('categories'),'author' => $this->request->getPost('author'),'created_at' => $this->request->getPost('created_at')];

        if (!empty($this->request->getPost('pageimg'))) {
            $data['seo']['coverImage'] = $this->request->getPost('pageimg');
            $data['seo']['IMGWidth'] = $this->request->getPost('pageIMGWidth');
            $data['seo']['IMGHeight'] = $this->request->getPost('pageIMGHeight');
        }
        if (!empty($this->request->getPost('description'))) $data['seo']['description'] = $this->request->getPost('description');

        $insertID = $this->commonModel->createOne('blog', $data);
        if ($insertID) {
            if (!empty($this->request->getPost('keywords'))) $this->commonTagsLib->checkTags($this->request->getPost('keywords'), 'blogs', (string)$insertID, 'tags');
            return redirect()->route('blogs', [1])->with('message', '<b>' . $this->request->getPost('title') . '</b> adlı blog oluşturuldu.');
        } else return redirect()->back()->withInput()->with('error', 'Blog oluşturulamadı.');
    }

    public function edit(string $id)
    {
        $this->defData['tags'] = $this->model->limitTags_ajax(['pivot.tagType' => 'blogs', 'pivot.piv_id' => new ObjectId($id)], []);
        $t = [];
        foreach ($this->defData['tags'] as $tag) {
            $t[] = ['id' => (string)$tag->_id->id, 'value' => $tag->_id->value];
        }
        $this->defData['categories'] = $this->commonModel->getList('categories');
        $this->defData['infos'] = $this->commonModel->getOne('blog', ['_id' => new ObjectId($id)]);
        $this->defData['tags'] = json_encode($t);
        $this->defData['authors']=$this->commonModel->getList('users',['status'=>'active']);
        unset($t);
        return view('Modules\Backend\Views\blog\update', $this->defData);
    }

    public function update(string $id)
    {
        $valData = ([
            'title' => ['label' => 'Sayfa Başlığı', 'rules' => 'required'],
            'seflink' => ['label' => 'Sayfa URL', 'rules' => 'required'],
            'content' => ['label' => 'İçerik', 'rules' => 'required'],
            'isActive' => ['label' => 'Yayın veya taslak', 'rules' => 'required'],
            'categories' => ['label' => 'Kategoriler', 'rules' => 'required'],
            'author' => ['label' => 'Yazar', 'rules' => 'required'],
            'created_at' => ['label' => 'Oluşturulma Tarihi', 'rules' => 'required|valid_date[d.m.Y H:i:s]']
        ]);
        if (!empty($this->request->getPost('pageimg'))) {
            $valData['pageimg'] = ['label' => 'Görsel URL', 'rules' => 'required|valid_url'];
            $valData['pageIMGWidth'] = ['label' => 'Görsel Genişliği', 'rules' => 'required|is_natural_no_zero'];
            $valData['pageIMGHeight'] = ['label' => 'Görsel Yüksekliği', 'rules' => 'required|is_natural_no_zero'];
        }
        if (!empty($this->request->getPost('description'))) $valData['description'] = ['label' => 'Seo Açıklaması', 'rules' => 'required'];
        if (!empty($this->request->getPost('keywords'))) $valData['keywords'] = ['label' => 'Seo Anahtar Kelimeleri', 'rules' => 'required'];
        if ($this->validate($valData) == false) return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        $info = $this->commonModel->getOne('blog', ['_id' => new ObjectId($id)]);
        if ($info->seflink != $this->request->getPost('seflink') && $this->commonModel->get_where(['seflink' => $this->request->getPost('seflink')], 'categories') === 1) return redirect()->back()->withInput()->with('error', 'Blog seflink adresi daha önce kullanılmış. lütfen kontrol ederek bir daha oluşturmayı deneyeyiniz.');
        $data = ['title' => $this->request->getPost('title'), 'content' => $this->request->getPost('content'), 'isActive' => (bool)$this->request->getPost('isActive'), 'seflink' => $this->request->getPost('seflink'), 'categories' => $this->request->getPost('categories'),'author' => $this->request->getPost('author'),'created_at' => $this->request->getPost('created_at')];

        if (!empty($this->request->getPost('pageimg'))) {
            $data['seo']['coverImage'] = $this->request->getPost('pageimg');
            $data['seo']['IMGWidth'] = $this->request->getPost('pageIMGWidth');
            $data['seo']['IMGHeight'] = $this->request->getPost('pageIMGHeight');
        }
        if (!empty($this->request->getPost('description'))) $data['seo']['description'] = $this->request->getPost('description');

        if ($this->commonModel->updateOne('blog', ['_id' => new ObjectId($id)], $data)) {
            if (!empty($this->request->getPost('keywords'))) $this->commonTagsLib->checkTags($this->request->getPost('keywords'), 'blogs', $id, 'tags');
            return redirect()->route('blogs', [1])->with('message', '<b>' . $this->request->getPost('title') . '</b> adlı blog oluşturuldu.');
        } else return redirect()->back()->withInput()->with('error', 'Blog oluşturulamadı.');
    }

    public function delete($id = null)
    {
        if ($this->commonModel->deleteMany('tags_pivot', ['piv_id' => new ObjectId($id), 'tagType' => 'blogs'])) {
            if ($this->commonModel->deleteOne('blog', ['_id' => new ObjectId($id)]) === true) return redirect()->route('blogs', [1])->with('message', 'blog silindi.');
            else return redirect()->back()->withInput()->with('error', 'Blog Silinemedi.');
        } else return redirect()->back()->withInput()->with('error', 'Blog Silinemedi.');
    }

    public function commentList()
    {
        return view('Modules\Backend\Views\blog\commentList',$this->defData);
    }

    public function commentResponse()
    {
        if ($this->request->isAJAX()) {
            $data = clearFilter($this->request->getPost());
            if (empty($data['search']['value'])) unset($data['search']);
            unset($data['columns'], $data['order']);
            $searchData=['isApproved'=>true];
            if (!empty($data['search']['value'])) $searchData['comFullName'] = new Regex($data['search']['value'], 'i');
            if ($data['length'] > 0) $results = $this->commonModel->getList('comments',$searchData, ['limit'=>(int)$data['length'], 'skip'=>(int)$data['start']]);
            else $results = $this->commonModel->getList('comments',$searchData);
            $c = ((int)$data['start']>0)?(int)$data['start']+1:1;
            $data = [
                'draw' => intval($data['draw']),
                "iTotalRecords" => $this->commonModel->count('comments',$searchData),
                "iTotalDisplayRecords" => $this->commonModel->count('comments',$searchData),
            ];
            foreach ($results as $result) {
                $id = (string)$result->_id;
                $data['aaData'][] = ['id' => $c,
                    'com_name_surname' => $result->comFullName,
                    'email' => $result->comEmail,
                    'created_at'=>$result->created_at,
                    'status'=>($result->isApproved==true)?'Approved':'Not approved',
                    'process'=>'<a href="'.route_to('blogUpdate', $result->_id) .'"
                                   class="btn btn-outline-info btn-sm">'.lang('Backend.update').'</a>
                                <a href="'. route_to('blogDelete', $result->_id).'"
                                   class="btn btn-outline-danger btn-sm">'.lang('Backend.delete').'</a>'];
                $c++;
            }
            if (!empty($data)) return $this->respond($data, 200);
            else return $this->respond(['message' => 'Not Found data'], 204);
        }
    }

    public function commentPendingApproval(){
        dd('commentPendingApproval');
    }
    public function confirmComment(){
        dd('confirmComment');
    }
    public function badwordList(){
        dd('badwordList');
    }
    public function badwordsAdd(){
        dd('badwordsAdd');
    }
}
