<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class BaseRepository implements RepositoryInterface
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    public function __construct()
    {
        $this->setModel();
    }

    /**
     * Get the model instance for the repository.
     *
     * @return string
     */
    abstract public function getModel();

    /**
     * Set the model instance for the repository.
     *
     * @return void
     */
    public function setModel()
    {
        $this->model = app()->make(
            $this->getModel()
        );
    }

    public function getAll()
    {
        return $this->model->all();
    }

    /**
     * Find a record by its primary key.
     *
     * @param int $id The primary key value.
     *
     * @return \Illuminate\Database\Eloquent\Model|null The found model or null if not found.
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record in the database.
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create($attributes = [])
    {
        return $this->model->create($attributes);
    }

    /**
     * Update a record by its primary key.
     *
     * @param int $id The primary key value.
     * @param array $attributes The data to update.
     * @return bool|null Whether the update was successful or not.
     */
    public function update($id, $attributes = [])
    {
        $result = $this->find($id);
        if ($result) {
            return $result->update($attributes);
        }
        return false;
    }

    /**
     * Delete a record by its primary key.
     *
     * @param int $id The primary key value.
     * @return bool|null True if the deletion was successful, false otherwise.
     */
    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            return $result->delete();
        }
        return false;
    }

    /**
     * Create a record and retrive id of newly added record.
     *
     * @param array $attributes
     *
     * @return int
     */
    public function insertGetId($attributes = [])
    {
        return $this->model->insertGetId($attributes);
    }
}
