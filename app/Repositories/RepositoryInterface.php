<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * Get all
     * @return mixed
     */
    public function getAll();

    /**
     * Find a record by its primary key.
     *
     * @param int $id The primary key value.
     * @return \Illuminate\Database\Eloquent\Model|null The found model or null if not found.
     */
    public function find($id);

    /**
     * Create
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create($attributes = []);

    /**
     * Update a record by its primary key.
     *
     * @param int $id The primary key value.
     * @param array $attributes The data to update.
     * @return mixed Whether the update was successful or not.
     */
    public function update($id, $attributes = []);

    /**
     * Delete a record by its primary key.
     *
     * @param int $id The primary key value.
     * @return mixed True if the deletion was successful, false otherwise.
     */
    public function delete($id);

    /**
     * Create a record and retrive id of newly added record.
     *
     * @param array $attributes
     *
     * @return int
     */
    public function insertGetId($attributes = []);
}
