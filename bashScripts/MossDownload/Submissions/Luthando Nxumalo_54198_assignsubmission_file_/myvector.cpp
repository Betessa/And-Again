#include "myvector.h"
bool Thing::verbose = false;
size_t Thing::last_alloc = 0;

/**
 * @brief MyVector::MyVector Construct a vector with size 0
 *
 * Remember that the data pointer should point to nothing, and
 * counter variables should be initialised.
 */
MyVector::MyVector()
{
    n_items = 0;
    n_allocated= 0;
    data = nullptr;
}

/**
 * @brief MyVector::~MyVector Free any memory that you have allocated
 */
MyVector::~MyVector()
{
    delete[] data;
}

/**
 * @brief MyVector::size
 * @return The number of items in the vector
 */
size_t MyVector::size() const
{
 return n_items;
}

/**
 * @brief MyVector::allocated_length
 * @return The lenght of the allocated data buffer
 */
size_t MyVector::allocated_length() const
{
    return n_allocated;
}

/**
 * @brief MyVector::push_back
 * @param t The thing to add
 *
 * Add a thing to the back of the vector.
 * Remember to check if there is enough space to insert it first.
 * If there is not enough space, then you should reallocate the buffer
 * and copy each thing accross. When expanding the buffer, double the
 * allocated size.
 */
void MyVector::push_back(const Thing &t)
{
    size_t new_size;



   if ( n_allocated == 0)
   {
        n_allocated++;
        data = new Thing [n_allocated];
        data[n_allocated -1] = t;
        reallocate(n_allocated);
   }
   else
   {
       if (n_items > n_allocated)
       {
           new_size = n_allocated * 2;
        reallocate(new_size);
       }
       else
       {
           data[n_items] = t;
       }
   }
     n_items++;
}

/**
 * @brief MyVector::pop_back
 * Remove the last item from the back.
 * Reallocate with half the space if less than a quarter of the vector is used.
 */
void MyVector::pop_back()
{
 Thing * ptr = nullptr;
 for (int i = 0 ; i < (n_items-1) ; ++i )
 {
     ptr [i] = data[i];
 }

 if (n_items  < (1/4)*n_allocated)
 {
    n_allocated = n_allocated/2;
     reallocate(n_allocated);
 }
 data = ptr;
n_items = n_items - 1;
 delete ptr;


}

/**
 * @brief MyVector::front
 * @return A reference to the first item in the array.
 * I will never call this on an empty list.
 */
Thing &MyVector::front()
{
    Thing ptr = data[0];
    return ptr;

}

/**
 * @brief MyVector::back
 * @return A reference to the last item in the array.
 *
 * Note that this might not be the back of the data buffer.
 * I will never call this on an empty list.
 */
Thing &MyVector::back()
{

    Thing ptr = data[n_items];
    return ptr;


}

/**
 * @brief MyVector::begin
 * @return A pointer to the first thing.
 */
Thing *MyVector::begin()
{
    Thing ptr = data[0];
    return &ptr;


}

/**
 * @brief MyVector::end
 * @return A pointer to the memory address following the last thing.
 */
Thing *MyVector::end()
{
    Thing ptr = data[n_items];
    return &ptr;

}size_t new_size;

/**
 * @brief MyVector::operator []
 * @param i
 * @return A reference to the ith item in the list.
 */
Thing &MyVector::operator[](size_t i)
{
    if (i < n_items)
    {
        return data[i];
    }
    else{
       throw ("Requested index out of bounds.");
    }


}

/**
 * @brief MyVector::at
 * @param i
 * @return A reference to the ith item in the list after checking
 * that the index is not out of bounds.
 */
Thing &MyVector::at(size_t i)
{
    if (i < n_items)
    {
       return data[i];
    }
    else{
       throw ("Requested index out of bounds.");
    }

}

/**
 * @brief MyVector::reallocate
 * @param new_size
 * Reallocate the memory buffer to be "new_size" length, using new Thing[new_size]
 * Copy all items from the old buffer into the new one.
 * Delete the old buffer using delete[]
 */
void MyVector::reallocate(size_t new_size)
{
 Thing * p = new Thing [new_size];
 for (int i = 0 ; i < n_items ; ++i)
 {
     p[i] = data[i];
 }

 delete [] data;
 data = p;

 n_allocated = new_size;

}

