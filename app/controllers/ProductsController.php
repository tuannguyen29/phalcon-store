<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use \Products;

class ProductsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    public function showAction()
    {
        var_dump('show-product');
        die;
    }

    public function getAction($param1, $param2 = 0)
    {
        var_dump($param1, $param2);
        var_dump($this->dispatcher->getParam('lang'));
        die;
    }

    /**
     * Searches for products
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Products', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $products = Products::find($parameters);
        if (count($products) == 0) {
            $this->flash->notice("The search did not find any products");

            $this->dispatcher->forward([
                "controller" => "products",
                "action"     => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $products,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {
    }

    /**
     * Edits a product
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {
            $product = Products::findFirstByid($id);
            if (!$product) {
                $this->flash->error("product was not found");

                $this->dispatcher->forward([
                    'controller' => "products",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $product->id;

            $this->tag->setDefault("id", $product->id);
            $this->tag->setDefault("code", $product->code);
            $this->tag->setDefault("name", $product->name);
            $this->tag->setDefault("quantity", $product->quantity);
            $this->tag->setDefault("price", $product->price);
            $this->tag->setDefault("status", $product->status);
            $this->tag->setDefault("created_at", $product->created_at);
            $this->tag->setDefault("updated_at", $product->updated_at);
        }
    }

    /**
     * Creates a new product
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "products",
                'action' => 'index'
            ]);

            return;
        }

        $product = new Products();
        $product->code = $this->request->getPost("code");
        $product->name = $this->request->getPost("name");
        $product->quantity = $this->request->getPost("quantity");
        $product->price = $this->request->getPost("price");
        $product->status = $this->request->getPost("status");
        $product->createdAt = $this->request->getPost("created_at");
        $product->updatedAt = $this->request->getPost("updated_at");


        if (!$product->save()) {
            foreach ($product->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "products",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("product was created successfully");

        $this->dispatcher->forward([
            'controller' => "products",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a product edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "products",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $product = Products::findFirstByid($id);

        if (!$product) {
            $this->flash->error("product does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "products",
                'action'     => 'index'
            ]);

            return;
        }

        $product->code = $this->request->getPost("code");
        $product->name = $this->request->getPost("name");
        $product->quantity = $this->request->getPost("quantity");
        $product->price = $this->request->getPost("price");
        $product->status = $this->request->getPost("status");
        $product->createdAt = $this->request->getPost("created_at");
        $product->updatedAt = $this->request->getPost("updated_at");


        if (!$product->save()) {
            foreach ($product->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "products",
                'action'     => 'edit',
                'params'     => [$product->id]
            ]);

            return;
        }

        $this->flash->success("product was updated successfully");

        $this->dispatcher->forward([
            'controller' => "products",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a product
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $product = Products::findFirstByid($id);
        if (!$product) {
            $this->flash->error("product was not found");

            $this->dispatcher->forward([
                'controller' => "products",
                'action' => 'index'
            ]);

            return;
        }

        if (!$product->delete()) {
            foreach ($product->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "products",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("product was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "products",
            'action' => "index"
        ]);
    }
}
