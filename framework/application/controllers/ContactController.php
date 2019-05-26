<?php

namespace application\controllers;

use application\core\Controller;
use application\infrastructure\Response;


class ContactController extends Controller
{
    /**
     * Получение списка контактов
     */
    public function listAction() {
        $contacts = $this->model->getContacts();
        Response::json($contacts);
    }

    /**
     * Удаление контакта
     */
    public function deleteAction() {

        $data = json_decode(file_get_contents("php://input"));
        $isDelete = $this->model->deleteContact($data->id);
        //Если удаление успешно, отвечаем кодом 200
        if($isDelete == 1) {
            $isDelete = Response::STATUS_OK;
        }

        Response::json($isDelete, "Cannot delete contact");
    }

    /**
     * Получение контакта
     */
    public function getAction() {

        $data = json_decode(file_get_contents("php://input"));
        $result = $this->model->getContact($data->id);

        Response::json($result, 'Contact not found');
    }

    /**
     * Создание контакта
     */
    public function createAction() {

        $data = json_decode(file_get_contents("php://input"));

        $result = $this->model->createContact($data->name, $data->phones);

        Response::json($result, 'Cannot create the contact');
    }

    /**
     * Редактирование контакта
     */
    public function updateAction() {

        $data = json_decode(file_get_contents("php://input"));
        $result = $this->model->updateContact($data->id, $data->name, $data->phones);

        Response::json($result, "Cannot update the contact");
    }

    /**
     * Добавление номомера к контакту
     */
    public function addPhoneAction() {

        $data = json_decode(file_get_contents("php://input"));
        $result = $this->model->addPhone($data->contactId, $data->phone);

        Response::json($result, "Cannot add phone to contact");
    }
}
