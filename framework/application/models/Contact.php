<?php

namespace application\models;

use application\core\Model;
use PDOException;

class Contact extends Model
{
    public function getContacts() {

        $data = $this->db->row('
                SELECT contacts.id, contacts.name, phones.phone
                FROM contacts
                INNER JOIN phones ON phones.contact_id = contacts.id ORDER BY contacts.id ASC' );

        return $this->dataFormatter($data);
    }

    public function deleteContact($id) {

        return $this->db->delete('DELETE FROM contacts WHERE id = :id', ["id" => $id]);
    }

    public function getContact(int $id) {

        $data = $this->db->row('
                SELECT contacts.id, contacts.name, phones.phone
                FROM phones
                INNER JOIN contacts ON contacts.id = phones.contact_id WHERE contacts.id = :id ORDER BY contacts.id DESC', ["id" => $id]);

        return $this->dataFormatter($data, true);
    }

    public function createContact(string $name, array $phones) {
        try {
            $this->db->connection->beginTransaction();
            $this->db->create('INSERT INTO contacts (name) VALUES(:name)', ["name" => $name]);
            $contactId = (int)$this->db->connection->lastInsertId();

            foreach ($phones as $phone) {
                $this->db->create('INSERT INTO phones (contact_id, phone) VALUES(:contact_id, :phone)',
                    ["contact_id" => $contactId, "phone" => $phone]);
            }

            $this->db->connection->commit();
        } catch (PDOException $e) {
            return false;
        }

        return $this->getContact($contactId);
    }

    public function updateContact(int $contactId, string $name, array $phones) {
        try {
            $this->db->connection->beginTransaction();
            $this->db->create('UPDATE contacts SET name = :name WHERE contacts.id = :id', ["name" => $name, "id" => $contactId]);

            $this->db->delete("DELETE FROM phones WHERE contact_id = :contact_id", [ "contact_id" => $contactId]);
            foreach ($phones as $phone) {
                $this->db->create('INSERT INTO phones (contact_id, phone) VALUES(:contact_id, :phone)',
                    ["contact_id" => $contactId, "phone" => $phone]);
            }

            $this->db->connection->commit();
        } catch (PDOException $e) {
            return false;
        }

        return $this->getContact($contactId);
    }

    public function addPhone(int $contactId, string $phone) {

        $this->db->create('INSERT INTO phones (contact_id, phone) VALUES(:contact_id, :phone)',
            ["contact_id" => $contactId, "phone" => $phone]);

        return $this->getContact($contactId);
    }

    /**
     * @param array $data - массив данных
     * @param bool $isSingle - true если ожидаем один элемент в массиве
     * @return array|mixed
     */
    private function dataFormatter(array $data, bool $isSingle = false) {
        //Тут магия формирования нужного формата данных для фронта.
        $result = [];
        foreach ($data as $element) {
            if (!array_key_exists($element["id"], $result)) {
                $result[$element["id"]] = [
                    "id" => $element["id"],
                    "name" => $element["name"],
                    "phones" => [$element["phone"]]
                ];
            } else {
                $result[$element["id"]]["phones"][] = $element["phone"];
            }
        }

        if($isSingle) {
            $data = reset($result);
        } else {
            $data = array_values($result);
        }

        return $data;
    }
}
