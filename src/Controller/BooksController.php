<?php

namespace App\Controller;
use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\View\ViewBuilder;
use Cake\ORM\Query;
use Cake\Http\Response;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Text;
use App\Utility\CsvParser;

class BooksController extends AppController {

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        // $this->loadComponent('Paginator', ['limit' => 10]);
    }

    public function index()
    {
        $this->viewBuilder()->setLayout('ajax'); // Use an empty layout for AJAX requests
    }

    private $columns = [
        ['data' => 'id'],
        ['data' => 'book_name'],
        ['data' => 'image'],
        ['data' => 'category'],
        // Add more columns as needed
    ];

    public function data()
    {
        $this->autoRender = false;

        $requestData = $this->request->getData();
        $query = $this->Books->find();
    
        // Handle search input
        $searchValue = isset($requestData['search']['value']) ? $requestData['search']['value'] : '';
        if (!empty($searchValue)) {
            $query->where([
                'OR' => [
                    'book_name LIKE' => '%' . $searchValue . '%',
                    'category LIKE' => '%' . $searchValue . '%'
                ]
            ]);
        }
    
        // Apply sorting
        $sortColumn = isset($requestData['order'][0]['column']) ? intval($requestData['order'][0]['column']) : 0;
        $sortDirection = isset($requestData['order'][0]['dir']) ? $requestData['order'][0]['dir'] : 'asc';
        $sortField = $this->columns[$sortColumn]['data'];
        $query->order([$sortField => $sortDirection]);
    
        // Paginate the results
        $start = isset($requestData['start']) ? intval($requestData['start']) : 0;
        $length = isset($requestData['length']) ? intval($requestData['length']) : 10;
        $query->limit($length)->offset($start);
    
        // Fetch data from the database based on search filter and sorting
        $books = $query->toArray();
        
        $data = [];
        foreach ($books as $book) {
            $actions = '<a class="edit-btn" data-book-id="' . $book->id . '" id="#exampleModal" data-toggle="modal" data-target="#exampleModal" href="/library/books/edit/' . $book->id . '">Edit</a> | ';
            $actions .= '<a class="delete-btn" data-book-id="' . $book->id . '" href="/library/books/delete/' . $book->id . '">Delete</a>';
    
            $data[] = [
                'id' => $book->id,
                'book_name' => $book->book_name,
                'category' => $book->category,
                'image' => $book->image,
                'actions' => $actions, 
            ];
        }
    
        $totalRecords = $this->Books->find()->count();
    
        // Apply search filter count
        if (!empty($searchValue)) {
            $filteredRecords = $this->Books->find()
                ->where([
                    'OR' => [
                        'book_name LIKE' => '%' . $searchValue . '%',
                        'category LIKE' => '%' . $searchValue . '%'
                    ]
                ])
                ->count();
        } else {
            $filteredRecords = $totalRecords;
        }
    
        $output = [
            'draw' => intval($this->request->getData('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ];
    
        $this->response->getBody()->write(json_encode($output));
        return $this->response;
    }
    public function view($id = null)
    {
        $book = $this->book->get($id, [
            'contain' => []
        ]);

        $this->set('book', $book);
    }

    public function add() {

        $book = $this->Books->newEntity();
        if ($this->request->is('post')) {
            $postData = $this->request->getData();
            $book = $this->Books->newEntity($postData);
        
            if(!$postData['image']['name']){
                $filename = null;
            }else {
                if(!empty($postData['image']['name'])) {
                //getting file name
                $filename = $postData['image']['name'];
                //setting upload file path
                $uploadPath = WWW_ROOT.'/img/uploads/';
                //creating path for upload
                $uploadFile  = $uploadPath.$filename;
                    if((move_uploaded_file($postData['image']['tmp_name'], $uploadFile))) {
                          //after upload put into entiy the filename
                          $book['image'] = $filename;
                    }
                 }
                 $postData['image'] = !isset($filename) ? null : $filename;
                 $book = $this->Books->patchEntity($book, $postData);
                if ($this->Books->save($book)) {

                    // $this->Flash->success(__('The book has been saved.'));
                } else {
                    $this->Flash->error(__('The book could not be saved. Please, try again.'));
                }
            }
        }
        
        $this->set('book', $book);
    }

    public function edit($id){

        // Load the book entity to be edited
        $book = $this->Books->get($id);

        // debug($book->toArray());

        // Pass the book data to the edit view
        $this->set('book', $book);
    }

    public function update($id) {
        $book = $this->Books->get($id);
        if ($this->request->is(['post', 'put', 'patch'])) {
            $postData = $this->request->getData();
        
            // Check if a new image file has been uploaded
            if (!empty($postData['image']['name'])) {
                $filename = $postData['image']['name'];
                //setting upload file path
                $uploadPath = WWW_ROOT.'/img/uploads/';
                //creating path for upload
                $uploadFile  = $uploadPath.$filename;
                    if((move_uploaded_file($postData['image']['tmp_name'], $uploadFile))) {
                          //after upload put into entiy the filename
                          $book['image'] = $filename;
                    }
            }

            $postData['image'] = !isset($filename) ? null : $filename;
            // Update other book details
             $book = $this->Books->patchEntity($book, $postData);

            if ($this->Books->save($book)) {
                // $this->Flash->success(__('The book has been updated.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The book could not be updated. Please, try again.'));
            }
        }
}

    public function delete($id){ 

        $this->autoRender = false; // Disable view rendering
        $book = $this->Books->get($id);
        if ($this->Books->delete($book)) {
            $response = ['message' => 'Book deleted successfully'];
        } else {
            $response = ['message' => 'Error deleting the book'];
        }
        // Send a JSON response
        $this->response = $this->response->withType('application/json')
            ->withStringBody(json_encode($response));
        return $this->response;
    } 

    public function upload() {
        $books = $this->loadModel('Books');
        
        if ($this->request->is('post')) {
           $postData = $this->request->getData();

           if(!empty($postData['csv_file']['name'])){
               $row = 1;
               if (($handle = fopen($postData['csv_file']['tmp_name'], "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        if(!empty($row != 1)){
                                     $result[] = array(
                                    'book_name' => $data[0],
                                    'category' => $data[1]
                                );
                            }
                             $row++;
                        }
                         $books = $this->Books->newEntities($result);                       
                         if($this->Books->saveMany($books)){
                         return $this->redirect(['action' => 'index']);
                         }else{
                             $this->Flash->error(__('The product could not be save. duplicate record found.'));
                        }       
                }
                  fclose($handle);
                  return $this->redirect(['action' => 'index']);
           }
           else {
                $this->Flash->error(__('Please choose file to upload.'));
                return $this->redirect(['action' => 'add']);
           }
        }
    }

    public function export()
    {
        $books = $this->loadModel('books');
        $this->setResponse($this->getResponse()->withDownload('export.csv'));
        $data = $books->find('all');

        $_serialize = 'data';
        $_header = ['id', 'Book name', 'Category', 'Image'];
    
        $this->viewBuilder()->setClassName('CsvView.Csv');
        $this->set(compact('data', '_header', '_serialize'));
    } 
}

?>