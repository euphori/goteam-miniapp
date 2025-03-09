<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/api'

interface Board {
  id: number
  title: string
}

const router = useRouter()
const boards = ref<Board[]>([])
const isEditing = ref<{ [key: number]: boolean }>({})
const newTitle = ref<{ [key: number]: string }>({})
const isCreating = ref(false)
const newBoardTitle = ref('')
const showDeleteModal = ref(false)
const boardToDelete = ref<Board | null>(null)

const fetchBoards = async () => {
  try {
    const response = await api.get('/api/boards')
    boards.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('Error fetching boards:', error)
    boards.value = []
  }
}

const createBoard = async () => {
  if (!newBoardTitle.value.trim()) return

  try {
    const response = await api.post('/api/boards', { title: newBoardTitle.value })
    boards.value.push(response.data)
    newBoardTitle.value = ''
    isCreating.value = false
  } catch (error) {
    console.error('Error creating board:', error)
  }
}

const editBoard = (board: Board) => {
  isEditing.value[board.id] = true
  newTitle.value[board.id] = board.title
}

const saveBoardTitle = async (board: Board) => {
  if (!newTitle.value[board.id]?.trim()) return

  try {
    if (newTitle.value[board.id] !== board.title) {
      await api.put(`/api/boards/${board.id}`, { title: newTitle.value[board.id] })
      board.title = newTitle.value[board.id]
    }
  } catch (error) {
    console.error('Error updating board title:', error)
  }

  isEditing.value[board.id] = false
}

const cancelEdit = (boardId: number) => {
  isEditing.value[boardId] = false
  newTitle.value[boardId] = ''
}

const goToBoard = (boardId: number) => {
  if (!isEditing.value[boardId]) {
    router.push(`/boards/${boardId}`)
  }
}

const confirmDeleteBoard = (board: Board) => {
  boardToDelete.value = board
  showDeleteModal.value = true
}

const deleteBoard = async () => {
  if (!boardToDelete.value) return
  try {
    await api.delete(`/api/boards/${boardToDelete.value.id}`)
    boards.value = boards.value.filter((b) => b.id !== boardToDelete.value!.id)
  } catch (error) {
    console.error('Error deleting board:', error)
  }
  showDeleteModal.value = false
  boardToDelete.value = null
}

onMounted(fetchBoards)
</script>

<template>
  <header class="bg-gray-100 shadow-sm h-20">
    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
      <h1 class="text-3xl font-bold tracking-tight text-gray-900">My Boards</h1>
    </div>
  </header>

  <div class="bg-gray-100 w-full h-[calc(100vh-80px)] overflow-auto p-6">
    <div class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 sm:grid-cols-2 md-grid-cols-3 lg:grid-cols-4 gap-4">
        <div
          class="bg-gray-200 shadow-md rounded-lg p-4 hover:shadow-xl transition duration-200 cursor-pointer min-h-[120px] flex items-center justify-center text-center text-gray-600 font-semibold"
          @click="isCreating = true"
        >
          <span v-if="!isCreating">+ Create Board</span>
          <input
            v-else
            v-model="newBoardTitle"
            placeholder="Enter board title"
            class="border-b-2 border-gray-500 px-3 py-2 bg-transparent focus:outline-none w-full"
            @keydown.enter="createBoard"
            @blur="isCreating = false"
            autofocus
          />
        </div>

        <div
          v-for="board in boards"
          :key="board.id"
          class="relative bg-white shadow-md rounded-lg p-4 hover:shadow-xl transition duration-200 cursor-pointer min-h-[120px] min-w-[150px] flex items-center justify-center text-center"
          @click="!isEditing[board.id] && goToBoard(board.id)"
        >
          <button
            class="absolute top-2 right-2 text-gray-500 hover:text-red-500"
            @click.stop="confirmDeleteBoard(board)"
          >
            ✖
          </button>

          <div v-if="isEditing[board.id]" class="w-full">
            <input
              v-model="newTitle[board.id]"
              @blur="saveBoardTitle(board)"
              @keydown.enter="saveBoardTitle(board)"
              class="text-lg font-semibold border-b-2 border-gray-500 focus:outline-none bg-transparent w-full"
              autofocus
            />
          </div>
          <div
            v-else
            @click.stop="editBoard(board)"
            class="text-lg font-semibold truncate cursor-pointer"
          >
            {{ board.title }}
          </div>
        </div>
      </div>
    </div>
  </div>

  <div v-if="showDeleteModal" class="fixed inset-0 flex items-center justify-center bg-gray-800/75">
    <div class="bg-white p-6 rounded-lg shadow-lg relative z-10">
      <p class="text-lg font-semibold">Are you sure you want to delete this board?</p>
      <div class="mt-4 flex justify-end gap-4">
        <button
          class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
          @click="showDeleteModal = false"
        >
          Cancel
        </button>
        <button
          class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
          @click="deleteBoard"
        >
          Delete
        </button>
      </div>
    </div>
  </div>
</template>
